<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Template
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('admin.partials.flash')

                <form method="POST" action="{{ route('admin.templates.update', $template) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Nome" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $template->name) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="category" value="Categoria" />
                            <x-text-input id="category" name="category" type="text" class="mt-1 block w-full" value="{{ old('category', $template->category) }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('category')" />
                        </div>
                        <div>
                            <x-input-label for="language" value="Idioma" />
                            <x-text-input id="language" name="language" type="text" class="mt-1 block w-full" value="{{ old('language', $template->language) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('language')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="content" value="Conteúdo" />
                        <textarea id="content" name="content" class="mt-1 block w-full rounded-md border-gray-300" rows="4">{{ old('content', $template->content) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('content')" />
                    </div>

                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300">
                            @foreach (['draft', 'submitted', 'approved', 'rejected'] as $status)
                                <option value="{{ $status }}" {{ old('status', $template->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status')" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Salvar</x-primary-button>
                        <a href="{{ route('admin.templates.index') }}" class="text-sm text-gray-600 hover:underline">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
