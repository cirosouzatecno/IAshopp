<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        $keys = [
            'whatsapp_provider',
            'whatsapp_webjs_base_url',
            'whatsapp_webhook_token',
            'whatsapp_access_token',
            'whatsapp_phone_number_id',
            'whatsapp_verify_token',
            'whatsapp_app_secret',
            'whatsapp_api_version',
            'handoff_message',
            'ai_enabled',
            'ai_provider',
            'ai_api_key',
            'ai_model',
            'ai_temperature',
            'ai_max_tokens',
            'ai_system_prompt',
            'ai_fallback_message',
            'pix_key',
            'pix_key_type',
            'pix_receiver_name',
            'pix_city',
            'pix_qr_image_path',
        ];

        $settings = Setting::query()
            ->whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'whatsapp_provider' => ['nullable', 'string', 'in:meta,webjs'],
            'whatsapp_webjs_base_url' => ['nullable', 'string'],
            'whatsapp_webhook_token' => ['nullable', 'string'],
            'whatsapp_access_token' => ['nullable', 'string'],
            'whatsapp_phone_number_id' => ['nullable', 'string'],
            'whatsapp_verify_token' => ['nullable', 'string'],
            'whatsapp_app_secret' => ['nullable', 'string'],
            'whatsapp_api_version' => ['nullable', 'string'],
            'handoff_message' => ['nullable', 'string'],
            'ai_enabled' => ['nullable', 'string'],
            'ai_provider' => ['nullable', 'string'],
            'ai_api_key' => ['nullable', 'string'],
            'ai_model' => ['nullable', 'string'],
            'ai_temperature' => ['nullable', 'string'],
            'ai_max_tokens' => ['nullable', 'string'],
            'ai_system_prompt' => ['nullable', 'string'],
            'ai_fallback_message' => ['nullable', 'string'],
            'pix_key' => ['nullable', 'string'],
            'pix_key_type' => ['nullable', 'string'],
            'pix_receiver_name' => ['nullable', 'string'],
            'pix_city' => ['nullable', 'string'],
            'pix_qr_image' => ['nullable', 'image', 'max:4096'],
        ]);

        foreach ($data as $key => $value) {
            if ($key === 'pix_qr_image') {
                continue;
            }
            Setting::setValue($key, $value);
        }

        if ($request->hasFile('pix_qr_image')) {
            $path = $request->file('pix_qr_image')->store('pix', 'public');
            $old = Setting::getValue('pix_qr_image_path');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            Setting::setValue('pix_qr_image_path', $path);
        }

        return redirect()->route('admin.settings.edit')
            ->with('status', 'Configurações atualizadas com sucesso.');
    }
}
