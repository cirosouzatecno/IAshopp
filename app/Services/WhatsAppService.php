<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendText(string $to, string $body): ?string
    {
        if ($this->useWebJs()) {
            return $this->sendViaWebJs($to, [
                'text' => $body,
            ]);
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $body,
            ],
        ];

        return $this->sendPayload($to, $payload);
    }

    public function sendImage(string $to, string $imageUrl, ?string $caption = null): ?string
    {
        if ($this->useWebJs()) {
            return $this->sendViaWebJs($to, [
                'image_url' => $imageUrl,
                'caption' => $caption,
            ], '/send-image');
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'image',
            'image' => [
                'link' => $imageUrl,
            ],
        ];

        if ($caption) {
            $payload['image']['caption'] = $caption;
        }

        return $this->sendPayload($to, $payload);
    }

    public function sendButtons(string $to, string $body, array $buttons, ?string $title = null, ?string $footer = null): ?string
    {
        $buttons = array_values(array_slice($buttons, 0, 3));

        if (empty($buttons)) {
            return $this->sendText($to, $body);
        }

        if ($this->useWebJs()) {
            return $this->sendViaWebJs($to, [
                'body' => $body,
                'buttons' => $buttons,
                'title' => $title,
                'footer' => $footer,
            ], '/send-buttons');
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $body,
                ],
                'action' => [
                    'buttons' => array_map(function (array $button) {
                        $id = $button['id'] ?? ($button['title'] ?? 'btn');
                        $title = $button['title'] ?? $button['body'] ?? 'Opcao';
                        return [
                            'type' => 'reply',
                            'reply' => [
                                'id' => (string) $id,
                                'title' => (string) $title,
                            ],
                        ];
                    }, $buttons),
                ],
            ],
        ];

        if ($title) {
            $payload['interactive']['header'] = [
                'type' => 'text',
                'text' => $title,
            ];
        }

        if ($footer) {
            $payload['interactive']['footer'] = [
                'text' => $footer,
            ];
        }

        return $this->sendPayload($to, $payload);
    }

    protected function useWebJs(): bool
    {
        $provider = Setting::getValue('whatsapp_provider', env('WHATSAPP_PROVIDER', 'meta'));

        return $provider === 'webjs';
    }

    protected function sendViaWebJs(string $to, array $data, string $path = '/send'): ?string
    {
        $baseUrl = Setting::getValue('whatsapp_webjs_base_url', env('WHATSAPP_WEBJS_BASE_URL', 'http://127.0.0.1:3001'));
        if (!$baseUrl) {
            Log::warning('WhatsApp Web base URL not configured.');
            return null;
        }

        $endpoint = rtrim($baseUrl, '/') . $path;
        $payload = array_merge([
            'to' => $to,
        ], $data);

        try {
            $response = Http::acceptJson()->post($endpoint, $payload);

            WhatsappMessage::create([
                'customer_id' => $this->resolveCustomerId($to),
                'direction' => 'outbound',
                'message_id' => $response->json('message_id'),
                'payload_json' => $payload,
                'status' => $response->successful() ? 'sent' : 'failed',
            ]);

            if (!$response->successful()) {
                Log::error('WhatsApp Web send failed', [
                    'to' => $to,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return $response->json('message_id');
        } catch (\Throwable $e) {
            Log::error('WhatsApp Web send exception', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    protected function sendPayload(string $to, array $payload): ?string
    {
        $token = Setting::getValue('whatsapp_access_token', env('WHATSAPP_ACCESS_TOKEN'));
        $phoneNumberId = Setting::getValue('whatsapp_phone_number_id', env('WHATSAPP_PHONE_NUMBER_ID'));
        $apiVersion = Setting::getValue('whatsapp_api_version', env('WHATSAPP_API_VERSION', 'v20.0'));

        if (!$token || !$phoneNumberId) {
            Log::warning('WhatsApp credentials missing. Message not sent.', ['to' => $to]);
            return null;
        }

        $url = "https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages";

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->post($url, $payload);

            $messageId = $response->json('messages.0.id');

            WhatsappMessage::create([
                'customer_id' => $this->resolveCustomerId($to),
                'direction' => 'outbound',
                'message_id' => $messageId,
                'payload_json' => $payload,
                'status' => $response->successful() ? 'sent' : 'failed',
            ]);

            if (!$response->successful()) {
                Log::error('WhatsApp send failed', [
                    'to' => $to,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return $messageId;
        } catch (\Throwable $e) {
            Log::error('WhatsApp send exception', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    protected function resolveCustomerId(string $phone): ?int
    {
        $customer = \App\Models\Customer::query()->firstOrCreate(['phone' => $phone]);

        return $customer?->id;
    }
}
