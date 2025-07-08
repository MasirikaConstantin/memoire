<?php

namespace App\Livewire\Violations;

use App\Models\Violation;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $filterStatus = '';
    public $perPage = 10;
    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => '']
    ];

    public function render()
    {
        $violations = Violation::with('plate')
            ->when($this->search, function ($query) {
                $query->whereHas('plate', function ($q) {
                    $q->where('number', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('traiter', $this->filterStatus);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.violations.index', [
            'violations' => $violations,
            'types' => Violation::types()
        ]);
    }

    public function delete(Violation $violation)
    {
        if ($violation->photo_preuve) {
            Storage::disk('public')->delete($violation->photo_preuve);
        }
        $violation->delete();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Violation supprimée avec succès'
        ]);
    }

    public function toggleStatus(Violation $violation)
    {
        $violation->update(['traiter' => !$violation->traiter]);
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Statut mis à jour'
        ]);
    }
}