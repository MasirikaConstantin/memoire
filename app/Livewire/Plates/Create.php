<?php

namespace App\Livewire\Plates;

use App\Models\Plate;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public Plate $plate;
    public $image;
    #[Validate('required|string|max:20|unique:plates,number')]
    public $number;
    #[Validate('nullable|string|max:255')]
    public $proprietaire;
    #[Validate('nullable|string|max:100')]
    public $type_vehicle;
    #[Validate('nullable|boolean')]
    public $est_volee = false;
    

    public function mount()
    {
        $this->plate = new Plate();
    }

    public function save()
    {
        try {
            $this->validate();
        } catch (\Exception $e) {
            dd($e->getMessage());
            return;
        }

        if ($this->image) {
            $this->plate->image = $this->image->store('plates', 'public');
        }

        $this->plate->create($this->validate());
        Flux::toast('Plaque créée avec succès');
        return redirect()->route('plates.index');
    }

    public function render()
    {
        return view('livewire.plates.create');
    }
}