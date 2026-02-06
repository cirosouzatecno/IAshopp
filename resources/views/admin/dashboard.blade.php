<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Painel
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('admin.partials.flash')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a class="border rounded-lg p-4 hover:bg-gray-50" href="{{ route('admin.products.index') }}">
                        <div class="font-semibold">Produtos</div>
                        <div class="text-sm text-gray-500">Gerencie o catálogo.</div>
                    </a>
                    <a class="border rounded-lg p-4 hover:bg-gray-50" href="{{ route('admin.orders.index') }}">
                        <div class="font-semibold">Pedidos</div>
                        <div class="text-sm text-gray-500">Acompanhe e atualize status.</div>
                    </a>
                    <a class="border rounded-lg p-4 hover:bg-gray-50" href="{{ route('admin.customers.index') }}">
                        <div class="font-semibold">Clientes</div>
                        <div class="text-sm text-gray-500">Veja histórico e dados.</div>
                    </a>
                    <a class="border rounded-lg p-4 hover:bg-gray-50" href="{{ route('admin.templates.index') }}">
                        <div class="font-semibold">Templates</div>
                        <div class="text-sm text-gray-500">Mensagens aprovadas e rascunhos.</div>
                    </a>
                    <a class="border rounded-lg p-4 hover:bg-gray-50" href="{{ route('admin.settings.edit') }}">
                        <div class="font-semibold">Configurações</div>
                        <div class="text-sm text-gray-500">WhatsApp e Pix.</div>
                    </a>
                    <a class="border rounded-lg p-4 hover:bg-gray-50" href="{{ route('admin.whatsapp-web.index') }}">
                        <div class="font-semibold">WhatsApp Web</div>
                        <div class="text-sm text-gray-500">Conectar via QR Code.</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
