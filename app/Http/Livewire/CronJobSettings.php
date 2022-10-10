<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CronJobSettings extends Component
{
    use LivewireAlert;
    public $cronTime;
    public $time;
    public function mount()
    {
        $this->cronTime = env('CRON_TIME');
    }

    public function updatedCronTime($value)
    {
        if (!Storage::get('cron/time.txt')) {
            Storage::makeDirectory('cron');
            Storage::put('cron/time.txt', $value);
            Storage::get('cron/time.txt');
        } else {
            Storage::put('cron/time.txt', $value);
            Storage::get('cron/time.txt');
        }
        Artisan::call('config:clear');
        $this->alert('success', 'Guardado con éxito!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function render()
    {
        if (!Storage::get('cron/time.txt')) {
            Storage::makeDirectory('cron');
            Storage::put('cron/time.txt', '00:00');
            Storage::get('cron/time.txt');
        }
        $this->time = Storage::get('cron/time.txt');
        return view('livewire.cron-job-settings');
    }
}
