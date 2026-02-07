<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-100 leading-tight">
            Pedidos
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('admin.partials.flash')

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2">ID</th>
                                <th class="py-2">Cliente</th>
                                <th class="py-2">Total</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Entrega</th>
                                <th class="py-2">Criado</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr class="border-b">
                                    <td class="py-2">{{ $order->id }}</td>
                                    <td class="py-2">{{ $order->customer?->name ?? $order->customer?->phone }}</td>
                                    <td class="py-2">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                                    <td class="py-2">{{ $order->status }}</td>
                                    <td class="py-2">{{ ucfirst($order->delivery_type) }}</td>
                                    <td class="py-2">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="py-2 text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:underline">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-gray-500 dark:text-slate-300">Nenhum pedido encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
