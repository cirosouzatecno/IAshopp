<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pedido #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('admin.partials.flash')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Cliente</div>
                        <div class="font-semibold">{{ $order->customer?->name ?? '—' }}</div>
                        <div class="text-gray-600">{{ $order->customer?->phone }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Total</div>
                        <div class="font-semibold">R$ {{ number_format($order->total, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Itens</h3>
                <ul class="text-sm space-y-2">
                    @foreach ($order->items as $item)
                        <li class="flex justify-between border-b pb-2">
                            <span>{{ $item->product?->name }} x{{ $item->qty }}</span>
                            <span>R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Atualizar Pedido</h3>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300">
                            @foreach (['Aguardando Pagamento', 'Confirmado', 'Entregue'] as $status)
                                <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status')" />
                    </div>

                    <div>
                        <x-input-label for="delivery_type" value="Tipo de entrega" />
                        <select id="delivery_type" name="delivery_type" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="entrega" {{ $order->delivery_type === 'entrega' ? 'selected' : '' }}>Entrega</option>
                            <option value="retirada" {{ $order->delivery_type === 'retirada' ? 'selected' : '' }}>Retirada</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('delivery_type')" />
                    </div>

                    <div>
                        <x-input-label for="address" value="Endereço" />
                        <textarea id="address" name="address" class="mt-1 block w-full rounded-md border-gray-300">{{ old('address', $order->address) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>

                    <div>
                        <x-input-label for="notes" value="Observações" />
                        <textarea id="notes" name="notes" class="mt-1 block w-full rounded-md border-gray-300">{{ old('notes', $order->notes) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Salvar</x-primary-button>
                        <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-600 hover:underline">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
