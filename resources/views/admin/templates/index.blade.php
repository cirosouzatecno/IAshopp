<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Templates
            </h2>
            <a href="{{ route('admin.templates.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Novo Template
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('admin.partials.flash')

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2">Nome</th>
                                <th class="py-2">Categoria</th>
                                <th class="py-2">Idioma</th>
                                <th class="py-2">Status</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($templates as $template)
                                <tr class="border-b">
                                    <td class="py-2">{{ $template->name }}</td>
                                    <td class="py-2">{{ $template->category ?? '—' }}</td>
                                    <td class="py-2">{{ $template->language }}</td>
                                    <td class="py-2">{{ $template->status }}</td>
                                    <td class="py-2 text-right">
                                        <a href="{{ route('admin.templates.edit', $template) }}" class="text-indigo-600 hover:underline">Editar</a>
                                        <form action="{{ route('admin.templates.destroy', $template) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline ml-3" onclick="return confirm('Remover template?')">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500">Nenhum template cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $templates->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
