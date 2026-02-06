<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\ConversationFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $expected = Setting::getValue('whatsapp_verify_token', env('WHATSAPP_VERIFY_TOKEN'));

        if ($mode === 'subscribe' && $token && $expected && hash_equals($expected, $token)) {
            return response($challenge, 200);
        }

        return response('Invalid verification token', 403);
    }

    public function receive(Request $request, ConversationFlow $flow)
    {
        if (!$this->validateSignature($request)) {
            return response('Invalid signature', 403);
        }

        $payload = $request->all();
        $flow->handleIncoming($payload);

        return response('ok', 200);
    }

    public function receiveWeb(Request $request, ConversationFlow $flow)
    {
        $token = Setting::getValue('whatsapp_webhook_token', env('WHATSAPP_WEBHOOK_TOKEN'));
        if ($token) {
            $headerToken = $request->header('X-Webhook-Token');
            if (!$headerToken || !hash_equals($token, $headerToken)) {
                return response('Invalid token', 403);
            }
        }

        $data = $request->validate([
            'from' => ['required', 'string'],
            'text' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'raw' => ['nullable', 'array'],
        ]);

        $flow->handleIncomingDirect(
            $data['from'],
            $data['text'] ?? '',
            $data['name'] ?? null,
            $data['raw'] ?? []
        );

        return response('ok', 200);
    }

    protected function validateSignature(Request $request): bool
    {
        $appSecret = Setting::getValue('whatsapp_app_secret', env('WHATSAPP_APP_SECRET'));

        if (!$appSecret) {
            Log::warning('WhatsApp app secret not configured. Signature validation skipped.');
            return true;
        }

        $header = $request->header('X-Hub-Signature-256');
        if (!$header || !str_starts_with($header, 'sha256=')) {
            return false;
        }

        $signature = substr($header, 7);
        $payload = $request->getContent();
        $expected = hash_hmac('sha256', $payload, $appSecret);

        return hash_equals($expected, $signature);
    }
}
