<div>
    <div class="rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-6 text-base-100">
            {{ $isEdit ? 'Modifier la violation' : 'Créer une nouvelle violation' }}
        </h2>

        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Plaque -->
                <div class="md:col-span-2">
                    <label for="plate_id" class="label text-base-100">Plaque d'immatriculation*</label>
                    <select 
                        id="plate_id" 
                        wire:model="plate_id" 
                        class="input input-default"
                        required
                    >
                        <option value="">Sélectionnez une plaque</option>
                        @foreach($plates as $plate)
                            <option value="{{ $plate->id }}">{{ $plate->number }} - {{ $plate->proprietaire ?? 'Inconnu' }}</option>
                        @endforeach
                    </select>
                    @error('plate_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Type de violation -->
                <div>
                    <label for="type" class="label text-base-100">Type de violation*</label>
                    <select 
                        id="type" 
                        wire:model="type" 
                        class="input"
                        required
                    >
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Localisation -->
                <div>
                    <label for="localisation" class="label text-base-100">Localisation</label>
                    <input 
                        type="text" 
                        id="localisation" 
                        wire:model="localisation" 
                        class="input input-default"
                    >
                </div>

                <!-- Photo preuve -->
                <div class="md:col-span-2">
                    <label for="photo" class="label text-base-100">Photo preuve</label>
                    <input 
                        type="file" 
                        id="photo" 
                        wire:model="photo" 
                        class="input "
                        accept="image/*"
                    >
                    @error('photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    @if($isEdit && $violation->photo_preuve)
                        <div class="mt-2">
                            <img 
                                src="{{ asset('storage/'.$violation->photo_preuve) }}" 
                                alt="Photo preuve" 
                                class="h-20 object-cover rounded"
                            >
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('violations.index') }}" class="btn btn-secondary">
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ $isEdit ? 'Mettre à jour' : 'Créer' }}
                </button>
            </div>
        </form>
    </div>
</div>