<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-100 leading-tight">
            Nova Regra
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @include('admin.chatbot._tabs')

            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('admin.partials.flash')

                <form method="POST" action="{{ route('admin.chatbot-rules.store') }}" class="space-y-6">
                    @csrf

                    @include('admin.chatbot-rules._form', ['rule' => $rule, 'templates' => $templates])

                    <div class="flex items-center gap-3">
                        <x-primary-button>Salvar</x-primary-button>
                        <a href="{{ route('admin.chatbot-rules.index') }}" class="text-sm text-gray-600 hover:underline">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
