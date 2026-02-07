<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-100 leading-tight">
            WhatsApp Web (QR)
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">
                <div class="text-sm text-gray-600">
                    Provedor atual: <strong>{{ $provider === 'webjs' ? 'WhatsApp Web (QR)' : 'Meta Cloud API' }}</strong>
                </div>

                <div class="rounded border p-4">
                    <div class="text-sm text-gray-500 dark:text-slate-300 mb-2">Status</div>
                    <div id="wa-status" class="font-semibold text-gray-800 dark:text-slate-100">Carregando...</div>
                </div>

                <div id="wa-error-wrap" class="rounded border border-red-200 bg-red-50 p-4 hidden">
                    <div class="text-sm text-red-700 mb-1">Erro</div>
                    <div id="wa-error" class="text-sm text-red-800"></div>
                    <div id="wa-error-at" class="text-xs text-red-600 mt-1 hidden"></div>
                </div>

                <div class="rounded border p-4">
                    <div class="text-sm text-gray-500 dark:text-slate-300 mb-2">QR Code</div>
                    <div class="flex items-center justify-center h-64 bg-gray-50 dark:bg-slate-900 rounded">
                        <img id="wa-qr" src="" alt="QR Code" class="max-h-56 hidden" />
                        <div id="wa-qr-placeholder" class="text-gray-500 dark:text-slate-300">Aguardando QR...</div>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-slate-300 mt-2">
                        Escaneie com o WhatsApp no celular para conectar.
                    </div>
                </div>

                <div class="text-sm text-gray-500 dark:text-slate-300">
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
            const errorWrap = document.getElementById('wa-error-wrap');
            const errorText = document.getElementById('wa-error');
            const errorAt = document.getElementById('wa-error-at');

            console.log('[WhatsApp QR] Iniciando...', { baseUrl });

            function renderError(data) {
                if (data && data.lastError) {
                    errorText.textContent = data.lastError;
                    errorWrap.classList.remove('hidden');
                    if (data.lastErrorAt) {
                        errorAt.textContent = `Ultimo erro: ${data.lastErrorAt}`;
                        errorAt.classList.remove('hidden');
                    } else {
                        errorAt.classList.add('hidden');
                    }
                } else {
                    errorWrap.classList.add('hidden');
                }
            }

            async function fetchStatus() {
                try {
                    console.log('[fetchStatus] Buscando status...');
                    const res = await fetch(`${baseUrl}/status`);
                    if (!res.ok) {
                        throw new Error(`HTTP ${res.status}`);
                    }
                    const data = await res.json();
                    console.log('[fetchStatus] Status recebido:', data);
                    statusEl.textContent = data.status ?? 'desconhecido';
                    renderError(data);

                    if (data.status === 'ready') {
                        qrImg.classList.add('hidden');
                        qrPlaceholder.textContent = 'Conectado';
                        qrPlaceholder.classList.remove('hidden');
                    }
                } catch (err) {
                    console.error('[fetchStatus] Erro:', err);
                    statusEl.textContent = 'offline';
                    renderError({ lastError: `Serviço offline: ${err.message}` });
                }
            }

            async function fetchQr() {
                try {
                    console.log('[fetchQr] Buscando QR code...');
                    const res = await fetch(`${baseUrl}/qr`);
                    if (!res.ok) {
                        throw new Error(`HTTP ${res.status}`);
                    }
                    const data = await res.json();
                    console.log('[fetchQr] Resposta:', {
                        status: data.status,
                        hasQrImage: !!data.qrImage,
                        qrImageSize: data.qrImage ? data.qrImage.length : 0
                    });

                    if (data.qrImage) {
                        console.log('[fetchQr] ✓ Renderizando QRImage');
                        qrImg.src = data.qrImage;
                        qrImg.classList.remove('hidden');
                        qrPlaceholder.classList.add('hidden');
                    } else if (data.status === 'ready') {
                        console.log('[fetchQr] Cliente já autenticado');
                        qrImg.classList.add('hidden');
                        qrPlaceholder.textContent = 'Conectado';
                        qrPlaceholder.classList.remove('hidden');
                    } else {
                        console.log('[fetchQr] QRImage ainda não disponível, status:', data.status);
                    }
                } catch (err) {
                    console.error('[fetchQr] Erro:', err);
                    qrImg.classList.add('hidden');
                    qrPlaceholder.textContent = `Serviço offline: ${err.message}`;
                    qrPlaceholder.classList.remove('hidden');
                }
            }

            // Executar testes iniciais
            console.log('[WhatsApp QR] Testando conectividade com', baseUrl);
            fetchStatus();
            fetchQr();

            // Atualizar a cada 5 segundos
            setInterval(fetchStatus, 5000);
            setInterval(fetchQr, 5000);

            console.log('[WhatsApp QR] ✓ Inicializado, atualizando a cada 5 segundos');
        })();
    </script>
</x-app-layout>
