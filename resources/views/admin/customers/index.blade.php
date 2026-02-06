<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Clientes
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @include('admin.partials.flash')

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2">ID</th>
                                <th class="py-2">Nome</th>
                                <th class="py-2">Telefone</th>
                                <th class="py-2">Última interação</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $customer)
                                <tr class="border-b">
                                    <td class="py-2">{{ $customer->id }}</td>
                                    <td class="py-2">{{ $customer->name ?? '—' }}</td>
                                    <td class="py-2">{{ $customer->phone }}</td>
                                    <td class="py-2">{{ $customer->last_interaction_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="py-2 text-right">
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="text-indigo-600 hover:underline">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500">Nenhum cliente registrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
