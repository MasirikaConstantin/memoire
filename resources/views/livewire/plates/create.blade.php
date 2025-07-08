<div>
    <div class=" rounded-lg shadow p-6 ">
        <h2 class="text-xl font-bold mb-6 text-base-100">
            {{ 'Créer une nouvelle plaque' }}
        </h2>

        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Numéro de plaque -->
                <div>
                    <flux:input 
                        :label="__('Numero Plaque')"
                        type="text" 
                        id="number" 
                        wire:model="number" 
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
                        wire:model="proprietaire" 
                        class="input-default"
                    />
                </div>

                <!-- Type de véhicule -->
                <div>
                    <flux:input 
                        :label="__('Type de véhicule')"
                        type="text" 
                        id="type_vehicle" 
                        wire:model="type_vehicle" 
                        class="input-default"
                    />
                </div>

                <!-- Statut volé -->
                <div>
                    <flux:field variant="inline">
                        <flux:checkbox wire:model="est_volee" />
                        <flux:label>{{ __('Statut volé') }}</flux:label>
                        <flux:error name="est_volee" />
                    </flux:field>
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
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('plates.index') }}" class="btn btn-secondary">
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary" x-on:click="$flux.toast('Your changes have been saved.')">
                    {{ 'Créer' }}
                </button>
            </div>
        </form>
    </div>
</div>