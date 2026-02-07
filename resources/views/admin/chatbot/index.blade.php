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
                    Nova regra
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('admin.chatbot._tabs')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 md:col-span-3">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-slate-300">Menu principal do bot</div>
                            <div class="text-lg font-semibold text-gray-800 dark:text-slate-100 mt-1">
                                {{ $menuRule ? $menuRule->name : 'Ainda nao criado' }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-slate-300 mt-1">
                                Use botoes para guiar o cliente: Catalogo, Status e Atendente.
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($menuRule)
                                <a href="{{ route('admin.chatbot-rules.edit', $menuRule) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                    Editar menu
                                </a>
                            @else
                                <a href="{{ route('admin.chatbot-rules.create', ['preset' => 'menu']) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                    Criar menu
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 dark:text-slate-300">Regras ativas</div>
                    <div class="text-2xl font-semibold text-gray-800 dark:text-slate-100 mt-2">{{ $rulesCount }}</div>
                    <div class="mt-4">
                        <a href="{{ route('admin.chatbot-rules.index') }}" class="text-indigo-600 text-sm hover:underline">Gerenciar regras</a>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 dark:text-slate-300">Templates</div>
                    <div class="text-2xl font-semibold text-gray-800 dark:text-slate-100 mt-2">{{ $templatesCount }}</div>
                    <div class="mt-4">
                        <a href="{{ route('admin.templates.index') }}" class="text-indigo-600 text-sm hover:underline">Gerenciar templates</a>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 dark:text-slate-300">IA habilitada</div>
                    <div class="text-2xl font-semibold text-gray-800 dark:text-slate-100 mt-2">{{ $aiEnabled ? 'Sim' : 'Nao' }}</div>
                    <div class="mt-4">
                        <a href="{{ route('admin.settings.edit') }}" class="text-indigo-600 text-sm hover:underline">Configurar IA</a>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mt-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-slate-100">Ultimas regras</h3>
                    <a href="{{ route('admin.chatbot-rules.index') }}" class="text-sm text-gray-500 dark:text-slate-300 hover:underline">Ver todas</a>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b dark:border-slate-700">
                                <th class="py-2">Nome</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Tipo</th>
                                <th class="py-2">Prioridade</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestRules as $rule)
                                <tr class="border-b dark:border-slate-700">
                                    <td class="py-2">{{ $rule->name }}</td>
                                    <td class="py-2">{{ $rule->status }}</td>
                                    <td class="py-2">{{ $rule->response_type }}</td>
                                    <td class="py-2">{{ $rule->priority }}</td>
                                    <td class="py-2 text-right">
                                        <a href="{{ route('admin.chatbot-rules.edit', $rule) }}" class="text-indigo-600 hover:underline">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500 dark:text-slate-300">Nenhuma regra cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
