<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-100 leading-tight">
                Chatbot
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.chatbot-rules.create', ['preset' => 'menu']) }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                    Criar menu rapido
                </a>
                <a href="{{ route('admin.chatbot-rules.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Nova Regra
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('admin.chatbot._tabs')

            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('admin.partials.flash')

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b dark:border-slate-700">
                                <th class="py-2">Nome</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Match</th>
                                <th class="py-2">Resposta</th>
                                <th class="py-2">Estado</th>
                                <th class="py-2">Prioridade</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rules as $rule)
                                <tr class="border-b dark:border-slate-700">
                                    <td class="py-2">{{ $rule->name }}</td>
                                    <td class="py-2">{{ $rule->status }}</td>
                                    <td class="py-2">{{ $rule->match_type }}</td>
                                    <td class="py-2">{{ $rule->response_type }}</td>
                                    <td class="py-2">{{ $rule->applies_to_state ?? 'any' }}</td>
                                    <td class="py-2">{{ $rule->priority }}</td>
                                    <td class="py-2 text-right">
                                        <a href="{{ route('admin.chatbot-rules.edit', $rule) }}" class="text-indigo-600 hover:underline">Editar</a>
                                        <form action="{{ route('admin.chatbot-rules.destroy', $rule) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline ml-3" onclick="return confirm('Remover regra?')">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-gray-500 dark:text-slate-300">Nenhuma regra cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $rules->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
