<?php

namespace App\Livewire;

use App\Models\Kehadiran;
use Livewire\Component;


class Map extends Component
{
    public function render()
    {
        $kehadiran = Kehadiran::with('user')->get();

        return view('livewire.map',[
            'kehadiran' => $kehadiran
        ]);
    }
}
