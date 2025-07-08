<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-base-100">Gestion des Violations</h1>
        <a href="{{ route('violations.create') }}" class="btn btn-primary">
            Ajouter une violation
        </a>
    </div>

    <div class="bg-whsite rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <input 
                    type="text" 
                    wire:model.lazy="search" 
                    placeholder="Rechercher par plaque..." 
                    class="input input-default"
                >
            </div>
            <div>
                <select wire:model="filterType" class="input input-default">
                    <option value="">Tous les types</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model="filterStatus" class="input input-default">
                    <option value="">Tous les statuts</option>
                    <option value="0">Non traité</option>
                    <option value="1">Traité</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table text-base-100">
                <thead>
                    <tr class="text-base-100">
                        <th>Plaque</th>
                        <th>Type</th>
                        <th>Localisation</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($violations as $violation)
                        <tr class="text-base-100">
                            <td>
                                {{ $violation->plate->number }}
                            </td>
                            <td>
                                {{ $types[$violation->type] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $violation->localisation ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button 
                                    wire:click="toggleStatus({{ $violation->id }})"
                                    class="{{ $violation->traiter ? 'badge badge-success' : 'badge badge-danger' }} cursor-pointer"
                                >
                                    {{ $violation->traiter ? 'Traité' : 'Non traité' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $violation->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                <a href="{{ route('violations.edit', $violation) }}" class="btn btn-secondary">
                                    Éditer
                                </a>
                                <button 
                                    wire:click="delete({{ $violation->id }})" 
                                    onclick="confirm('Êtes-vous sûr?') || event.stopImmediatePropagation()"
                                    class="btn btn-error"
                                >
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Aucune violation trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $violations->links() }}
        </div>
    </div>
</div>