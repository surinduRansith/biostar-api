<?php

namespace App\Livewire;

use App\Models\shift;
use Livewire\Component;

class ShiftCreate extends Component
{
    public $shiftName;
    public $shiftId;
    public $inTime;
    public $outTime;

    public $editshift=false;

    public $shiftlists;

    public function mount()
    {
        
        $this->shiftlists=shift::all();

    }
    public function createShift() {

       
        $this->validate([
            'shiftName' => 'required',
            'inTime' => 'required',
            'outTime' => 'required',
        ]);

        shift::create([
            'shiftname' => $this->shiftName,
            'start_time' => $this->inTime,
            'end_time' => $this->outTime,
        ]);

        $this->reset('shiftName', 'inTime', 'outTime');
       $this->mount();
    }

    public function deleteShift($id)
    {
        shift::find($id)->delete();
        $this->mount();
    }

   
    public function render()
    {
        return view('livewire.shift-create');
    }
}
