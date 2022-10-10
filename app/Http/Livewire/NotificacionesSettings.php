<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class NotificacionesSettings extends Component
{
    use LivewireAlert;

    public $email;

    protected $rules = [
        'email' => 'required|email'
    ];

    protected $messages = [
        'email.required' => 'El campo correo electrónico es requerido',
        'email.email' => 'El campo correo electrónico no es de un formato de tipo email "example@example.com"'
    ];

    public function mount()
    {
        $this->email = Storage::get('email/email.txt');
    }

    public function updatedEmail($value)
    {
        $this->validate();
    }

    public function save()
    {
        $this->validate();
        Storage::makeDirectory('email');
        if (Storage::get('email/email.txt')) {
            Storage::delete('email.txt');
        }
        Storage::put('email/email.txt', $this->email);
        $this->alert('success', 'Guardado con éxito!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function render()
    {
        return view('livewire.notificaciones-settings');
    }
}
