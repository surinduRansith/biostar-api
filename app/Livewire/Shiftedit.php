<?php

namespace App\Livewire;

use App\Models\shift;
use Livewire\Component;

class Shiftedit extends Component
{

    public $shiftName;
    public $shiftId;
    public $inTime;
    public $outTime;

    public $editshift=false;

    public $shiftlists;    
    
    public function mount()
    {
        $this->editshift = true;
        $shift = shift::find($this->shiftId);
        $this->shiftName = $shift->Shiftname;
        $this->inTime = $shift->start_time;
        $this->outTime = $shift->end_time;
        $this->shiftId = $shift->id;

    }
   

    public function updateShift()
    {
        
        $this->validate([
            'shiftName' => 'required',
            'inTime' => 'required',
            'outTime' => 'required',
        ]);

        $shift = shift::find($this->shiftId);
        $shift->update([
            'shiftname' => $this->shiftName,
            'start_time' => $this->inTime,
            'end_time' => $this->outTime,
        ]);

        $this->reset('shiftName', 'inTime', 'outTime');
        return redirect()->to('shiftcreate');
    }
    
    public function render()
    {
        return view('livewire.shiftedit');
    }
}
