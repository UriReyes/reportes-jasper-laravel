<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class DeleteStateStored extends Component
{
    use LivewireAlert;

    public $state;
    public $stateAll;

    public function mount()
    {
        $this->state = 'state-msp\state.json';
        $this->stateAll = 'state-msp-all\state.json';
    }

    public function render()
    {
        return view('livewire.delete-state-stored');
    }

    public function deleteState($file)
    {

        Storage::delete('public/' . str_replace('/', '\\', $file));
        $this->alert('success', '¡Bien Hecho!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'text' => 'Eliminado con éxito',
        ]);
    }
}
