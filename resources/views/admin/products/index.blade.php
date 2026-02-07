<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-100 leading-tight">
                Produtos
            </h2>
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Novo Produto
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-100 overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('admin.partials.flash')

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2">ID</th>
                                <th class="py-2">Nome</th>
                                <th class="py-2">Preço</th>
                                <th class="py-2">Estoque</th>
                                <th class="py-2">Ativo</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr class="border-b">
                                    <td class="py-2">{{ $product->id }}</td>
                                    <td class="py-2">{{ $product->name }}</td>
                                    <td class="py-2">R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                    <td class="py-2">{{ $product->stock }}</td>
                                    <td class="py-2">{{ $product->active ? 'Sim' : 'Não' }}</td>
                                    <td class="py-2 text-right">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:underline">Editar</a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline ml-3" onclick="return confirm('Remover produto?')">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-gray-500 dark:text-slate-300">Nenhum produto cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
