<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    public function isEnabled(): bool
    {
        $enabled = Setting::getValue('ai_enabled', '0');
        return $enabled === '1' || $enabled === 'true';
    }

    public function generateReply(string $message, Customer $customer): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $provider = Setting::getValue('ai_provider', 'openai');
        if ($provider !== 'openai') {
            return null;
        }

        $apiKey = Setting::getValue('ai_api_key', env('OPENAI_API_KEY'));
        if (!$apiKey) {
            Log::warning('AI key missing. Skipping AI response.');
            return null;
        }

        $model = Setting::getValue('ai_model', 'gpt-4o-mini');
        $temperature = (float) Setting::getValue('ai_temperature', '0.4');
        $maxTokens = (int) Setting::getValue('ai_max_tokens', '300');

        $systemPrompt = Setting::getValue('ai_system_prompt') ?: $this->defaultSystemPrompt();
        $context = $this->buildContext($customer);

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'temperature' => $temperature,
                    'max_completion_tokens' => $maxTokens,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt . "\n\n" . $context],
                        ['role' => 'user', 'content' => $message],
                    ],
                ]);

            if (!$response->successful()) {
                Log::warning('AI response failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $content = $response->json('choices.0.message.content');
            if (!is_string($content) || trim($content) === '') {
                return null;
            }

            return trim($content);
        } catch (\Throwable $e) {
            Log::error('AI request error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function buildContext(Customer $customer): string
    {
        $lines = [];
        $lines[] = 'Cliente: ' . ($customer->name ?: 'sem nome');
        $lines[] = 'Telefone: ' . $customer->phone;

        $products = Product::query()
            ->where('active', true)
            ->orderBy('id')
            ->limit(20)
            ->get();

        if ($products->isEmpty()) {
            $lines[] = 'Catalogo: sem produtos ativos.';
            return implode("\n", $lines);
        }

        $lines[] = 'Catalogo (id | nome | preco | estoque):';
        foreach ($products as $product) {
            $price = number_format((float) $product->price, 2, ',', '.');
            $lines[] = "#{$product->id} | {$product->name} | R$ {$price} | {$product->stock}";
        }

        $lines[] = 'Oriente o cliente a informar o ID do produto e a quantidade para comprar.';

        return implode("\n", $lines);
    }

    protected function defaultSystemPrompt(): string
    {
        return implode("\n", [
            'Voce e um assistente de vendas via WhatsApp.',
            'Responda sempre em PT-BR, de forma objetiva.',
            'Ajude o cliente a escolher produtos e a concluir o pedido.',
            'Nao invente dados. Use o catalogo fornecido.',
            'Se o cliente pedir atendente, responda pedindo para digitar "atendente".',
            'Se pedir status, oriente a digitar "status".',
            'Evite respostas longas; faca perguntas curtas para avancar a venda.',
        ]);
    }
}
