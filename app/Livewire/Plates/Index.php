<?php

namespace App\Livewire\Plates;

use App\Models\Plate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    protected $queryString = ['search' => ['except' => '']];

    public function render()
    {
        return view('livewire.plates.index', [
            'plates' => Plate::when($this->search, function ($query) {
                $query->where('number', 'like', '%'.$this->search.'%')
                    ->orWhere('proprietaire', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate($this->perPage)
        ]);
    }

    public function delete(Plate $plate)
    {
        if ($plate->image) {
            Storage::disk('public')->delete($plate->image);
        }
        $plate->delete();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Plaque supprimée avec succès'
        ]);
    }
}