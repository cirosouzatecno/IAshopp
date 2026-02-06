<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            WhatsApp Web (QR)
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">
                <div class="text-sm text-gray-600">
                    Provedor atual: <strong>{{ $provider === 'webjs' ? 'WhatsApp Web (QR)' : 'Meta Cloud API' }}</strong>
                </div>

                <div class="rounded border p-4">
                    <div class="text-sm text-gray-500 mb-2">Status</div>
                    <div id="wa-status" class="font-semibold text-gray-800">Carregando...</div>
                </div>

                <div class="rounded border p-4">
                    <div class="text-sm text-gray-500 mb-2">QR Code</div>
                    <div class="flex items-center justify-center h-64 bg-gray-50 rounded">
                        <img id="wa-qr" src="" alt="QR Code" class="max-h-56 hidden" />
                        <div id="wa-qr-placeholder" class="text-gray-500">Aguardando QR...</div>
                    </div>
                    <div class="text-xs text-gray-500 mt-2">
                        Escaneie com o WhatsApp no celular para conectar.
                    </div>
                </div>

                <div class="text-sm text-gray-500">
                    Base URL do serviço: <code>{{ $baseUrl }}</code>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const baseUrl = @json($baseUrl);
            const statusEl = document.getElementById('wa-status');
            const qrImg = document.getElementById('wa-qr');
            const qrPlaceholder = document.getElementById('wa-qr-placeholder');

            async function fetchStatus() {
                try {
                    const res = await fetch(`${baseUrl}/status`);
                    const data = await res.json();
                    statusEl.textContent = data.status ?? 'desconhecido';

                    if (data.status === 'ready') {
                        qrImg.classList.add('hidden');
                        qrPlaceholder.textContent = 'Conectado';
                        qrPlaceholder.classList.remove('hidden');
                    }
                } catch (err) {
                    statusEl.textContent = 'offline';
                }
            }

            async function fetchQr() {
                try {
                    const res = await fetch(`${baseUrl}/qr`);
                    const data = await res.json();
                    if (data.qrImage) {
                        qrImg.src = data.qrImage;
                        qrImg.classList.remove('hidden');
                        qrPlaceholder.classList.add('hidden');
                    } else if (data.status === 'ready') {
                        qrImg.classList.add('hidden');
                        qrPlaceholder.textContent = 'Conectado';
                        qrPlaceholder.classList.remove('hidden');
                    }
                } catch (err) {
                    qrImg.classList.add('hidden');
                    qrPlaceholder.textContent = 'Serviço offline';
                    qrPlaceholder.classList.remove('hidden');
                }
            }

            fetchStatus();
            fetchQr();
            setInterval(fetchStatus, 5000);
            setInterval(fetchQr, 5000);
        })();
    </script>
</x-app-layout>
