<?php

namespace App\Livewire\Plates;

use App\Models\Plate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public Plate $plate;
    public $image;
    public $isEdit = false;

    protected $rules = [
        'plate.number' => 'required|string|max:20|unique:plates,number',
        'plate.proprietaire' => 'nullable|string|max:255',
        'plate.type_vehicle' => 'nullable|string|max:100',
        'plate.est_volee' => 'boolean',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];

    public function mount(Plate $plate)
    {
        $this->plate = $plate ?? new Plate();
        $this->isEdit = $plate->exists;
        
        if ($this->isEdit) {
            $this->rules['plate.number'] = 'required|string|max:20|unique:plates,number,'.$plate->id;
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->image) {
            if ($this->isEdit && $this->plate->image) {
                Storage::disk('public')->delete($this->plate->image);
            }
            $this->plate->image = $this->image->store('plates', 'public');
        }

        $this->plate->save();

        return redirect()->route('plates.index')->with('notify', [
            'type' => 'success',
            'message' => $this->isEdit ? 'Plaque mise à jour' : 'Plaque créée'
        ]);
    }

    public function render()
    {
        return view('livewire.plates.form');
    }
}