<div>
    <div class=" rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-6">
            {{ $isEdit ? 'Modifier la plaque' : 'Créer une nouvelle plaque' }}
        </h2>

        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Numéro de plaque -->
                <div>
                    <flux:input 
                        :label="__('Numero Plaque')"
                        type="text" 
                        id="number" 
                        wire:model="plate.number" 
                        class="input-default"
                        required
                    />
                </div>

                <!-- Propriétaire -->
                <div>
                    <flux:input 
                        :label="__('Proprietaire')"
                        type="text" 
                        id="proprietaire" 
                        wire:model="plate.proprietaire" 
                        class="input-default"
                    />
                </div>

                <!-- Type de véhicule -->
                <div>
                    <flux:input 
                        :label="__('Type de véhicule')"
                        type="text" 
                        id="type_vehicle" 
                        wire:model="plate.type_vehicle" 
                        class="input-default"
                    />
                </div>

                <!-- Statut volé -->
                <div>
                    <flux:checkbox 
                        :label="__('Statut volé')"
                        wire:model="plate.est_volee" 
                        class="rounded text-primary focus:ring-primary"
                    />
                </div>

                <!-- Image -->
                <div class="md:col-span-2">
                    <flux:input 
                        :label="__('Image')"
                        type="file" 
                        id="image" 
                        wire:model="image" 
                        class="input-default"
                        accept="image/*"
                    />

                    @if($isEdit && $plate->image)
                        <div class="mt-2">
                            <img 
                                src="{{ asset('storage/'.$plate->image) }}" 
                                alt="Image plaque" 
                                class="h-20 object-cover rounded"
                            >
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('plates.index') }}" class="btn-secondary">
                    Annuler
                </a>
                <button type="submit" class="btn-primary">
                    {{ $isEdit ? 'Mettre à jour' : 'Créer' }}
                </button>
            </div>
        </form>
    </div>
</div>