<?php

namespace App\Services;

use App\Models\ChatbotRule;
use App\Models\Conversation;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\WhatsappMessage;
use App\Services\AiService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConversationFlow
{
    public function __construct(private WhatsAppService $whatsApp, private AiService $ai)
    {
    }

    public function handleIncoming(array $payload): void
    {
        $messages = $this->extractMessages($payload);

        foreach ($messages as $message) {
            $this->processMessage($message);
        }
    }

    public function handleIncomingDirect(string $from, string $text, ?string $name = null, array $raw = []): void
    {
        $this->processMessage([
            'from' => $from,
            'text' => $text,
            'raw' => $raw,
            'contact_name' => $name,
        ]);
    }

    protected function extractMessages(array $payload): array
    {
        $messages = [];

        foreach (Arr::get($payload, 'entry', []) as $entry) {
            foreach (Arr::get($entry, 'changes', []) as $change) {
                $value = Arr::get($change, 'value', []);
                $contacts = Arr::get($value, 'contacts', []);
                $contactName = Arr::get($contacts, '0.profile.name');

                foreach (Arr::get($value, 'messages', []) as $msg) {
                    $text = $this->extractText($msg);
                    $messages[] = [
                        'from' => Arr::get($msg, 'from'),
                        'text' => $text,
                        'raw' => $msg,
                        'contact_name' => $contactName,
                    ];
                }
            }
        }

        return $messages;
    }

    protected function extractText(array $msg): string
    {
        $type = Arr::get($msg, 'type');

        if ($type === 'text') {
            return trim((string) Arr::get($msg, 'text.body', ''));
        }

        if ($type === 'button') {
            return trim((string) Arr::get($msg, 'button.text', ''));
        }

        if ($type === 'interactive') {
            $reply = Arr::get($msg, 'interactive.button_reply.title')
                ?? Arr::get($msg, 'interactive.button_reply.id')
                ?? Arr::get($msg, 'interactive.list_reply.title');
            $reply = $reply ?? Arr::get($msg, 'interactive.list_reply.id');

            return trim((string) $reply);
        }

        return '';
    }

    protected function processMessage(array $message): void
    {
        $phone = $message['from'];
        $text = $message['text'];

        if (!$phone) {
            return;
        }

        $customer = Customer::query()->firstOrCreate(
            ['phone' => $phone],
            ['name' => $message['contact_name']]
        );

        if ($message['contact_name'] && !$customer->name) {
            $customer->name = $message['contact_name'];
        }
        $customer->last_interaction_at = Carbon::now();
        $customer->save();

        WhatsappMessage::create([
            'customer_id' => $customer->id,
            'direction' => 'inbound',
            'payload_json' => $message['raw'],
            'status' => 'received',
        ]);

        $conversation = Conversation::query()->firstOrCreate(
            ['customer_id' => $customer->id],
            ['state' => 'welcome']
        );

        $conversation->last_message_at = Carbon::now();
        $conversation->save();

        $normalized = $this->normalize($text);

        if ($this->isHandoff($normalized)) {
            $conversation->state = 'handoff';
            $conversation->context_json = [];
            $conversation->save();
            $this->whatsApp->sendText($phone, $this->handoffMessage());
            return;
        }

        if ($this->isMenuCommand($normalized)) {
            $this->sendMenu($phone);
            $conversation->state = 'welcome';
            $conversation->context_json = [];
            $conversation->save();
            return;
        }

        if ($this->isStatusCommand($normalized)) {
            $this->sendLatestOrderStatus($phone, $customer);
            return;
        }

        if ($this->tryHandleChatbot($phone, $text, $normalized, $conversation, $customer)) {
            return;
        }

        switch ($conversation->state) {
            case 'welcome':
                $this->handleWelcome($phone, $normalized, $conversation);
                break;
            case 'browsing_catalog':
                $this->handleCatalogSelection($phone, $normalized, $conversation);
                break;
            case 'collect_qty':
                $this->handleQuantity($phone, $normalized, $conversation);
                break;
            case 'confirm_order':
                $this->handleConfirmOrder($phone, $normalized, $conversation, $customer);
                break;
            case 'delivery_choice':
                $this->handleDeliveryChoice($phone, $normalized, $conversation, $customer);
                break;
            case 'collect_address':
                $this->handleAddress($phone, $text, $conversation, $customer);
                break;
            case 'order_created':
                $this->sendMenu($phone);
                break;
            default:
                $this->sendMenu($phone);
                $conversation->state = 'welcome';
                $conversation->context_json = [];
                $conversation->save();
                break;
        }
    }

    protected function tryHandleChatbot(string $phone, string $text, string $normalized, Conversation $conversation, Customer $customer): bool
    {
        if (!$this->isChatbotState($conversation->state)) {
            return false;
        }

        if ($this->handleChatbotRules($phone, $text, $normalized, $conversation, $customer)) {
            return true;
        }

        return $this->handleAiFallback($phone, $text, $customer);
    }

    protected function isChatbotState(?string $state): bool
    {
        return in_array($state, ['welcome', 'order_created'], true);
    }

    protected function handleChatbotRules(string $phone, string $text, string $normalized, Conversation $conversation, Customer $customer): bool
    {
        $rules = ChatbotRule::query()
            ->active()
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->get();

        foreach ($rules as $rule) {
            if (!$rule->appliesToState($conversation->state)) {
                continue;
            }
            if (!$rule->matches($normalized)) {
                continue;
            }

            $this->dispatchChatbotRule($rule, $phone, $text, $conversation, $customer);
            return true;
        }

        return false;
    }

    protected function dispatchChatbotRule(ChatbotRule $rule, string $phone, string $text, Conversation $conversation, Customer $customer): void
    {
        switch ($rule->response_type) {
            case 'text':
                $body = $this->renderTemplateText($rule->response_text ?? '', $customer);
                if ($body !== '') {
                    $this->whatsApp->sendText($phone, $body);
                }
                return;
            case 'template':
                if ($rule->template) {
                    $body = $this->renderTemplateText($rule->template->content ?? '', $customer);
                    if ($body !== '') {
                        $this->whatsApp->sendText($phone, $body);
                    }
                }
                return;
            case 'buttons':
                $buttons = $rule->buttons();
                $body = $this->renderTemplateText($rule->response_text ?? 'Escolha uma opcao:', $customer);
                if (empty($buttons)) {
                    $this->whatsApp->sendText($phone, $body);
                    return;
                }

                $messageId = $this->whatsApp->sendButtons($phone, $body, $buttons);
                if (!$messageId) {
                    $this->whatsApp->sendText($phone, $this->buildButtonsFallback($body, $buttons));
                }
                return;
            case 'menu':
                $this->sendMenu($phone);
                $conversation->state = 'welcome';
                $conversation->context_json = [];
                $conversation->save();
                return;
            case 'catalog':
                $this->sendCatalog($phone);
                $conversation->state = 'browsing_catalog';
                $conversation->context_json = [];
                $conversation->save();
                return;
            case 'status':
                $this->sendLatestOrderStatus($phone, $customer);
                return;
            case 'handoff':
                $conversation->state = 'handoff';
                $conversation->context_json = [];
                $conversation->save();
                $this->whatsApp->sendText($phone, $this->handoffMessage());
                return;
            case 'ai':
                $this->handleAiFallback($phone, $text, $customer, true);
                return;
            default:
                return;
        }
    }

    protected function handleAiFallback(string $phone, string $text, Customer $customer, bool $force = false): bool
    {
        if (!$force && !$this->ai->isEnabled()) {
            return false;
        }

        $reply = $this->ai->generateReply($text, $customer);
        if ($reply) {
            $this->whatsApp->sendText($phone, $reply);
            return true;
        }

        $fallback = Setting::getValue('ai_fallback_message');
        if ($fallback) {
            $this->whatsApp->sendText($phone, $fallback);
            return true;
        }

        return false;
    }

    protected function renderTemplateText(string $text, Customer $customer): string
    {
        $name = $customer->name ?: 'cliente';
        $firstName = Str::before($name, ' ');

        return strtr($text, [
            '{name}' => $name,
            '{nome}' => $name,
            '{first_name}' => $firstName,
            '{primeiro_nome}' => $firstName,
            '{phone}' => $customer->phone,
        ]);
    }

    protected function buildButtonsFallback(string $body, array $buttons): string
    {
        $lines = [$body];
        $index = 1;
        foreach ($buttons as $button) {
            $title = $button['title'] ?? $button['body'] ?? '';
            if ($title !== '') {
                $lines[] = "{$index}) {$title}";
                $index++;
            }
        }

        return implode("\n", $lines);
    }

    protected function handleWelcome(string $phone, string $normalized, Conversation $conversation): void
    {
        if (in_array($normalized, ['1', 'catalogo', 'catálogo'], true)) {
            $this->sendCatalog($phone);
            $conversation->state = 'browsing_catalog';
            $conversation->context_json = [];
            $conversation->save();
            return;
        }

        if (in_array($normalized, ['2', 'pedido', 'status'], true)) {
            $customer = $conversation->customer;
            if ($customer) {
                $this->sendLatestOrderStatus($phone, $customer);
            }
            return;
        }

        if (in_array($normalized, ['3', 'atendente', 'humano'], true)) {
            $conversation->state = 'handoff';
            $conversation->context_json = [];
            $conversation->save();
            $this->whatsApp->sendText($phone, $this->handoffMessage());
            return;
        }

        if ($normalized === '' || $normalized === '0') {
            $this->sendMenu($phone);
            return;
        }

        $this->sendMenu($phone);
    }

    protected function handleCatalogSelection(string $phone, string $normalized, Conversation $conversation): void
    {
        $productId = (int) preg_replace('/\D+/', '', $normalized);

        if ($productId <= 0) {
            $this->whatsApp->sendText($phone, 'Informe o ID do produto para continuar.');
            return;
        }

        $product = Product::query()->where('active', true)->find($productId);

        if (!$product) {
            $this->whatsApp->sendText($phone, 'Produto não encontrado. Tente outro ID.');
            return;
        }

        $conversation->state = 'collect_qty';
        $conversation->context_json = [
            'product_id' => $product->id,
        ];
        $conversation->save();

        $this->whatsApp->sendText($phone, "Quantas unidades de {$product->name}?");
    }

    protected function handleQuantity(string $phone, string $normalized, Conversation $conversation): void
    {
        $qty = (int) preg_replace('/\D+/', '', $normalized);

        if ($qty <= 0) {
            $this->whatsApp->sendText($phone, 'Informe uma quantidade válida.');
            return;
        }

        $context = $conversation->context_json ?? [];
        $productId = $context['product_id'] ?? null;
        $product = $productId ? Product::query()->find($productId) : null;

        if (!$product) {
            $conversation->state = 'browsing_catalog';
            $conversation->context_json = [];
            $conversation->save();
            $this->sendCatalog($phone);
            return;
        }

        if ($product->stock < $qty) {
            $this->whatsApp->sendText($phone, "Estoque insuficiente. Disponível: {$product->stock}.");
            return;
        }

        $context['qty'] = $qty;
        $conversation->state = 'confirm_order';
        $conversation->context_json = $context;
        $conversation->save();

        $total = $qty * (float) $product->price;
        $summary = "Resumo do pedido:\n";
        $summary .= "{$product->name} x{$qty}\n";
        $summary .= "Total: R$ " . number_format($total, 2, ',', '.');

        $buttons = [
            ['id' => 'confirmar', 'title' => 'Confirmar'],
            ['id' => 'cancelar', 'title' => 'Cancelar'],
        ];

        $messageId = $this->whatsApp->sendButtons($phone, $summary, $buttons);
        if (!$messageId) {
            $this->whatsApp->sendText($phone, $summary . "\n\n1) Confirmar\n2) Cancelar");
        }
    }

    protected function handleConfirmOrder(string $phone, string $normalized, Conversation $conversation, Customer $customer): void
    {
        if (in_array($normalized, ['1', 'sim', 'confirmar'], true)) {
            $conversation->state = 'delivery_choice';
            $conversation->save();
            $buttons = [
                ['id' => 'entrega', 'title' => 'Entrega'],
                ['id' => 'retirada', 'title' => 'Retirada'],
            ];
            $messageId = $this->whatsApp->sendButtons($phone, 'Entrega ou retirada?', $buttons);
            if (!$messageId) {
                $this->whatsApp->sendText($phone, "Entrega ou retirada?\n1) Entrega\n2) Retirada");
            }
            return;
        }

        if (in_array($normalized, ['2', 'nao', 'não', 'cancelar'], true)) {
            $conversation->state = 'welcome';
            $conversation->context_json = [];
            $conversation->save();
            $this->sendMenu($phone);
            return;
        }

        $this->whatsApp->sendText($phone, 'Responda com 1 para confirmar ou 2 para cancelar.');
    }

    protected function handleDeliveryChoice(string $phone, string $normalized, Conversation $conversation, Customer $customer): void
    {
        $context = $conversation->context_json ?? [];

        if ($normalized === '1' || $normalized === 'entrega') {
            $context['delivery_type'] = 'entrega';
            $conversation->state = 'collect_address';
            $conversation->context_json = $context;
            $conversation->save();
            $this->whatsApp->sendText($phone, 'Informe o endereço completo para entrega.');
            return;
        }

        if ($normalized === '2' || $normalized === 'retirada') {
            $context['delivery_type'] = 'retirada';
            $conversation->context_json = $context;
            $conversation->save();
            $this->createOrderAndSendPayment($phone, $conversation, $customer, null);
            return;
        }

        $this->whatsApp->sendText($phone, 'Escolha 1 para entrega ou 2 para retirada.');
    }

    protected function handleAddress(string $phone, string $address, Conversation $conversation, Customer $customer): void
    {
        $address = trim($address);

        if ($address === '') {
            $this->whatsApp->sendText($phone, 'Informe um endereço válido.');
            return;
        }

        $this->createOrderAndSendPayment($phone, $conversation, $customer, $address);
    }

    protected function createOrderAndSendPayment(string $phone, Conversation $conversation, Customer $customer, ?string $address): void
    {
        $context = $conversation->context_json ?? [];
        $productId = $context['product_id'] ?? null;
        $qty = (int) ($context['qty'] ?? 0);

        $product = $productId ? Product::query()->find($productId) : null;

        if (!$product || $qty <= 0) {
            $this->sendMenu($phone);
            $conversation->state = 'welcome';
            $conversation->context_json = [];
            $conversation->save();
            return;
        }

        $total = $qty * (float) $product->price;

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => 'Aguardando Pagamento',
            'delivery_type' => $context['delivery_type'] ?? 'entrega',
            'address' => $address,
            'total' => $total,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => $qty,
            'unit_price' => $product->price,
            'total' => $total,
        ]);

        if ($product->stock >= $qty) {
            $product->stock = $product->stock - $qty;
            $product->save();
        }

        $conversation->state = 'order_created';
        $conversation->context_json = [];
        $conversation->save();

        $this->whatsApp->sendText(
            $phone,
            "Pedido #{$order->id} criado. Total: R$ " . number_format($total, 2, ',', '.')
        );

        $this->sendPixInstructions($phone, $order);
    }

    protected function sendPixInstructions(string $phone, Order $order): void
    {
        $pixKey = Setting::getValue('pix_key');
        $pixKeyType = Setting::getValue('pix_key_type');
        $pixReceiver = Setting::getValue('pix_receiver_name');
        $pixCity = Setting::getValue('pix_city');
        $pixQrPath = Setting::getValue('pix_qr_image_path');

        $lines = [
            'Pagamento via Pix',
            "Valor: R$ " . number_format($order->total, 2, ',', '.'),
        ];

        if ($pixKey) {
            $lines[] = "Chave Pix: {$pixKey}" . ($pixKeyType ? " ({$pixKeyType})" : '');
        }

        if ($pixReceiver) {
            $lines[] = "Recebedor: {$pixReceiver}";
        }

        if ($pixCity) {
            $lines[] = "Cidade: {$pixCity}";
        }

        $lines[] = 'Após o pagamento, aguarde a confirmação.';

        $this->whatsApp->sendText($phone, implode("\n", $lines));

        if ($pixQrPath) {
            $url = url('storage/' . ltrim($pixQrPath, '/'));
            $this->whatsApp->sendImage($phone, $url, 'QR Code Pix');
        }
    }

    protected function sendMenu(string $phone): void
    {
        $message = "Ola! Como posso ajudar?";
        $buttons = [
            ['id' => 'catalogo', 'title' => 'Catalogo'],
            ['id' => 'status', 'title' => 'Status'],
            ['id' => 'atendente', 'title' => 'Atendente'],
        ];

        $messageId = $this->whatsApp->sendButtons($phone, $message, $buttons);
        if (!$messageId) {
            $fallback = "Ola! Como posso ajudar?
";
            $fallback .= "1) Ver catalogo
";
            $fallback .= "2) Ver pedido/status
";
            $fallback .= "3) Falar com atendente";
            $this->whatsApp->sendText($phone, $fallback);
        }
    }

    protected function sendCatalog(string $phone): void
    {
        $products = Product::query()
            ->where('active', true)
            ->orderBy('id')
            ->get();

        if ($products->isEmpty()) {
            $this->whatsApp->sendText($phone, 'Não há produtos disponíveis no momento.');
            return;
        }

        $lines = ["Catálogo disponível:"];
        foreach ($products as $product) {
            $lines[] = "#{$product->id} - {$product->name} (R$ " . number_format($product->price, 2, ',', '.') . ")";
        }
        $lines[] = "Responda com o ID do produto.";

        $this->whatsApp->sendText($phone, implode("\n", $lines));
    }

    protected function sendLatestOrderStatus(string $phone, Customer $customer): void
    {
        $order = $customer->orders()->latest()->first();

        if (!$order) {
            $this->whatsApp->sendText($phone, 'Você ainda não possui pedidos.');
            return;
        }

        $message = "Pedido #{$order->id}\n";
        $message .= "Status: {$order->status}\n";
        $message .= "Total: R$ " . number_format($order->total, 2, ',', '.');

        $this->whatsApp->sendText($phone, $message);
    }

    protected function isHandoff(string $normalized): bool
    {
        if ($normalized === '3') {
            return true;
        }
        return str_contains($normalized, 'atendente')
            || str_contains($normalized, 'humano')
            || str_contains($normalized, 'falar com');
    }

    protected function isMenuCommand(string $normalized): bool
    {
        return in_array($normalized, ['menu', 'inicio', 'início', '0'], true);
    }

    protected function isStatusCommand(string $normalized): bool
    {
        return in_array($normalized, ['status', 'pedido', 'pedidos'], true);
    }

    protected function handoffMessage(): string
    {
        return Setting::getValue('handoff_message', 'Certo! Um atendente irá falar com você em instantes.');
    }

    protected function normalize(string $text): string
    {
        $text = trim(mb_strtolower($text));
        return preg_replace('/\s+/', ' ', $text) ?? '';
    }
}
