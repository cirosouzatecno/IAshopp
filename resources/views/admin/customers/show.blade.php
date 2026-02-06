<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Cliente #{{ $customer->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-sm">
                    <div><span class="text-gray-500">Nome:</span> {{ $customer->name ?? '—' }}</div>
                    <div><span class="text-gray-500">Telefone:</span> {{ $customer->phone }}</div>
                    <div><span class="text-gray-500">Última interação:</span> {{ $customer->last_interaction_at?->format('d/m/Y H:i') ?? '—' }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Pedidos</h3>
                <ul class="text-sm space-y-2">
                    @forelse ($customer->orders as $order)
                        <li class="flex items-center justify-between border-b pb-2">
                            <span>#{{ $order->id }} - {{ $order->status }}</span>
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:underline">Ver</a>
                        </li>
                    @empty
                        <li class="text-gray-500">Nenhum pedido encontrado.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
