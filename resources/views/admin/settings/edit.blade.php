<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Configurações
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('admin.partials.flash')

                <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <div>
                        <h3 class="font-semibold text-lg mb-4">WhatsApp</h3>

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="whatsapp_provider" value="Provedor" />
                                <select id="whatsapp_provider" name="whatsapp_provider" class="mt-1 block w-full rounded-md border-gray-300">
                                @php $provider = old('whatsapp_provider', $settings['whatsapp_provider'] ?? env('WHATSAPP_PROVIDER', 'meta')); @endphp
                                    <option value="meta" {{ $provider === 'meta' ? 'selected' : '' }}>Meta Cloud API (oficial)</option>
                                    <option value="webjs" {{ $provider === 'webjs' ? 'selected' : '' }}>WhatsApp Web (QR)</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="whatsapp_webjs_base_url" value="WhatsApp Web - Base URL" />
                                <x-text-input id="whatsapp_webjs_base_url" name="whatsapp_webjs_base_url" type="text" class="mt-1 block w-full" value="{{ old('whatsapp_webjs_base_url', $settings['whatsapp_webjs_base_url'] ?? 'http://127.0.0.1:3001') }}" />
                            </div>

                            <div>
                                <x-input-label for="whatsapp_webhook_token" value="Token Webhook (QR)" />
                                <x-text-input id="whatsapp_webhook_token" name="whatsapp_webhook_token" type="text" class="mt-1 block w-full" value="{{ old('whatsapp_webhook_token', $settings['whatsapp_webhook_token'] ?? '') }}" />
                            </div>

                            <div class="border-t pt-4">
                                <div class="text-sm text-gray-500">Meta Cloud API (oficial)</div>
                            </div>

                            <div>
                                <x-input-label for="whatsapp_access_token" value="Access Token" />
                                <x-text-input id="whatsapp_access_token" name="whatsapp_access_token" type="text" class="mt-1 block w-full" value="{{ old('whatsapp_access_token', $settings['whatsapp_access_token'] ?? '') }}" />
                            </div>

                            <div>
                                <x-input-label for="whatsapp_phone_number_id" value="Phone Number ID" />
                                <x-text-input id="whatsapp_phone_number_id" name="whatsapp_phone_number_id" type="text" class="mt-1 block w-full" value="{{ old('whatsapp_phone_number_id', $settings['whatsapp_phone_number_id'] ?? '') }}" />
                            </div>

                            <div>
                                <x-input-label for="whatsapp_verify_token" value="Verify Token" />
                                <x-text-input id="whatsapp_verify_token" name="whatsapp_verify_token" type="text" class="mt-1 block w-full" value="{{ old('whatsapp_verify_token', $settings['whatsapp_verify_token'] ?? '') }}" />
                            </div>

                            <div>
                                <x-input-label for="whatsapp_app_secret" value="App Secret" />
                                <x-text-input id="whatsapp_app_secret" name="whatsapp_app_secret" type="text" class="mt-1 block w-full" value="{{ old('whatsapp_app_secret', $settings['whatsapp_app_secret'] ?? '') }}" />
                            </div>

                            <div>
                                <x-input-label for="whatsapp_api_version" value="API Version" />
                                <x-text-input id="whatsapp_api_version" name="whatsapp_api_version" type="text" class="mt-1 block w-full" value="{{ old('whatsapp_api_version', $settings['whatsapp_api_version'] ?? 'v20.0') }}" />
                            </div>

                            <div>
                                <x-input-label for="handoff_message" value="Mensagem de Handoff" />
                                <textarea id="handoff_message" name="handoff_message" class="mt-1 block w-full rounded-md border-gray-300" rows="2">{{ old('handoff_message', $settings['handoff_message'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-lg mb-4">Pagamento Pix</h3>

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="pix_key" value="Chave Pix" />
                                <x-text-input id="pix_key" name="pix_key" type="text" class="mt-1 block w-full" value="{{ old('pix_key', $settings['pix_key'] ?? '') }}" />
                            </div>

                            <div>
                                <x-input-label for="pix_key_type" value="Tipo da Chave" />
                                <x-text-input id="pix_key_type" name="pix_key_type" type="text" class="mt-1 block w-full" value="{{ old('pix_key_type', $settings['pix_key_type'] ?? '') }}" />
                            </div>

                            <div>
                                <x-input-label for="pix_receiver_name" value="Recebedor" />
                                <x-text-input id="pix_receiver_name" name="pix_receiver_name" type="text" class="mt-1 block w-full" value="{{ old('pix_receiver_name', $settings['pix_receiver_name'] ?? '') }}" />
                            </div>

                            <div>
                                <x-input-label for="pix_city" value="Cidade" />
                                <x-text-input id="pix_city" name="pix_city" type="text" class="mt-1 block w-full" value="{{ old('pix_city', $settings['pix_city'] ?? '') }}" />
                            </div>

                            <div>
                                <x-input-label for="pix_qr_image" value="QR Code Pix (imagem)" />
                                @if (!empty($settings['pix_qr_image_path']))
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $settings['pix_qr_image_path']) }}" alt="QR Pix" class="h-24 rounded">
                                    </div>
                                @endif
                                <input id="pix_qr_image" name="pix_qr_image" type="file" class="mt-1 block w-full" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Salvar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
