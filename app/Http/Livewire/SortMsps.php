<?php

namespace App\Http\Livewire;

use App\Models\Msp;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class SortMsps extends Component
{
    use LivewireAlert;

    public $mspsSortByOrder;
    public $mspsSortByName;
    public $from = "";
    public $to = "";

    public function hydrate()
    {
        $this->emit('select2');
    }

    public function mount()
    {
        $this->mspsSortByOrder = $this->getMsps();
        $this->mspsSortByName = $this->getMsps('name');
    }
    public function getMsps($order = 'order')
    {
        return Msp::orderBy($order)->get();
    }

    public function updateMspOrder($data)
    {
        foreach ($data as $orderMsp) {
            $msp = Msp::where('name', $orderMsp['value'])->first();
            $msp->update(['order' => $orderMsp['order']]);
        }
        $this->mspsSortByOrder = $this->getMsps();
        $this->mspsSortByName = $this->getMsps('name');
    }

    public function changeMsps()
    {

        if ($this->from == "" || $this->to == "") {
            $this->alert('success', "Debes de seleccionar dos MSP para realizar el cambio");
            return;
        }
        $fromTxt = explode('|', $this->from);
        $toTxt = explode('|', $this->to);
        $fromOrder = $fromTxt[0];
        $fromName = $fromTxt[1];
        $toOrder = $toTxt[0];
        $toName = $toTxt[1];
        $mspFrom = Msp::where('name', $fromName)->first();
        $mspFrom->update(['order' => $toOrder]);
        $mspTo = Msp::where('name', $toName)->first();
        $mspTo->update(['order' => $fromOrder]);
        $this->from = "";
        $this->to = "";
        //Refresh msps
        $this->mspsSortByOrder = $this->getMsps();
        $this->mspsSortByName = $this->getMsps('name');
        $this->alert('success', "Se han cambiado las posiciones {$fromName} to {$toName}");
    }

    public function render()
    {
        return view('livewire.sort-msps');
    }
}
