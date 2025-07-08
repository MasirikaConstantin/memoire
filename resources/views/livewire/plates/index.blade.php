<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-base-100">Gestion des Plaques</h1>
        <a href="{{ route('plates.create') }}" class="btn btn-primary">
            Ajouter une plaque
        </a>
        
    </div>

    <div class=" rounded-lg shadow p-6">
        <div class="mb-4">
            <flux:input 
                :label="__('Rechercher')"
                type="text" 
                wire:model.live="search" 
                placeholder="Rechercher..." 
                class="input-default"
            />
        </div>

        <div class="overflow-x-auto">
            <table class="table text-base-100">
                <thead class="text-base-100">
                    <tr>
                        <th>Numéro</th>
                        <th>Propriétaire</th>
                        <th>Type véhicule</th>
                        <th>Volée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plates as $plate)
                        <tr>
                            <td>{{ $plate->number }}</td>
                            <td>{{ $plate->proprietaire ?? '-' }}</td>
                            <td>{{ $plate->type_vehicle ?? '-' }}</td>
                            <td>
                                @if($plate->est_volee)
                                    <span class="badge-danger">Oui</span>
                                @else
                                    <span class="badge-success">Non</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('plates.edit', $plate) }}" class="btn btn-secondary">
                                    Éditer
                                </a>
                                <button 
                                    wire:click="delete({{ $plate->id }})" 
                                    onclick="confirm('Êtes-vous sûr?') || event.stopImmediatePropagation()"
                                    class="btn btn-error"
                                >
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" >
                                Aucune plaque trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $plates->links() }}
        </div>
    </div>
</div>