<?php

namespace App\Livewire\Violations;

use App\Models\Plate;
use App\Models\Violation;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public Violation $violation;
    public $photo;
    public $isEdit = false;
    public $plates = [];
    #[Validate('required|exists:plates,id')]
    public $plate_id;
    #[Validate('required|string|in:feu_rouge,exces_de_vitesse,autre')]
    public $type = 'feu_rouge';
    #[Validate('nullable|string|max:255')]
    public $localisation;
    #[Validate('nullable|boolean')]
    public $traiter = false;
    public $types = [];

    public function mount(Violation $violation)
    {
        $this->violation = $violation;
        $this->isEdit = $violation->exists;
        $this->plates = Plate::orderBy('number')->get();
        $this->types = Violation::types();
        
        // Si c'est une édition, initialisez les valeurs
        if ($this->isEdit) {
            $this->plate_id = $violation->plate_id;
            $this->type = $violation->type;
            $this->localisation = $violation->localisation;
            $this->traiter = $violation->traiter;
        }
    }

    public function save()
    {
        try {
            $validated = $this->validate();
        } catch (\Exception $e) {
            dd($e->getMessage());
            return;
        }

        // Gestion de la photo
        if ($this->photo) {
            if ($this->isEdit && $this->violation->photo_preuve) {
                Storage::disk('public')->delete($this->violation->photo_preuve);
            }
            $validated['photo_preuve'] = $this->photo->store('violations', 'public');
        }

        // Mise à jour ou création
        if ($this->isEdit) {
            $this->violation->update($validated);
        } else {
            $this->violation = Violation::create($validated);
        }

        return redirect()->route('violations.index')->with('notify', [
            'type' => 'success',
            'message' => $this->isEdit ? 'Violation mise à jour' : 'Violation créée'
        ]);
    }

    public function render()
    {
        return view('livewire.violations.form');
    }
}