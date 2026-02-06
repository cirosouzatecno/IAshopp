<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Produto #{{ $product->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('admin.partials.flash')

                <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Nome" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $product->name) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Descrição" />
                        <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300">{{ old('description', $product->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="price" value="Preço" />
                            <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('price', $product->price) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>
                        <div>
                            <x-input-label for="stock" value="Estoque" />
                            <x-text-input id="stock" name="stock" type="number" class="mt-1 block w-full" value="{{ old('stock', $product->stock) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('stock')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="image" value="Imagem" />
                        @if ($product->image_path)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="Imagem do produto" class="h-24 rounded">
                            </div>
                        @endif
                        <input id="image" name="image" type="file" class="mt-1 block w-full" />
                        <x-input-error class="mt-2" :messages="$errors->get('image')" />
                    </div>

                    <div class="flex items-center">
                        <input id="active" name="active" type="checkbox" value="1" class="rounded border-gray-300" {{ old('active', $product->active) ? 'checked' : '' }}>
                        <label for="active" class="ml-2 text-sm text-gray-700">Ativo</label>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Salvar</x-primary-button>
                        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:underline">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
