<?php

namespace App\Livewire;

use Livewire\Component;

class Notification extends Component
{
    public $message;
    public $type;
    public $show = false;

    protected $listeners = ['notify' => 'showNotification'];

    public function showNotification($data)
    {
        $this->message = $data['message'];
        $this->type = $data['type'] ?? 'success';
        $this->show = true;

        $this->dispatchBrowserEvent('notification-shown');

        $this->emitSelf('startHideTimeout');
    }

    public function startHideTimeout()
    {
        $this->dispatchBrowserEvent('start-hide-timeout');
    }

    public function hide()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.notification');
    }
}