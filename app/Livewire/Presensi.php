<?php

namespace App\Livewire;

use App\Models\Cuti;
use App\Models\Jadwal;
use App\Models\Kehadiran;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Presensi extends Component
{
    public $latitude;
    public $longitude;
    public $insideRadius = false;
    public function render()
    {
        $jadwal = Jadwal::where('user_id', Auth::user()->id)->first();
        $kehadiran = Kehadiran::where('user_id', Auth::user()->id)
            ->whereDate('created_at', date('Y-m-d'))->first();
        // dd($jadwal->shift_id);
        // dd($kehadiran);
        return view('livewire.presensi', [
            'jadwal' => $jadwal,
            'kehadiran' => $kehadiran,
            'insideRadius' => $this->insideRadius,
        ]);
    }

    public function store()
    {
        // dd('>>>>>>>>>');
        $this->validate([
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        $hari_ini = Carbon::today()->format('Y-m-d');
        $setujuCuti = Cuti::where('user_id', Auth::user()->id)
            ->where('status', 'setuju')
            ->whereDate('tgl_mulai', '<=', $hari_ini)
            ->whereDate('tgl_akhir', '>=', $hari_ini)
            ->exists();
        if ($setujuCuti) {
            session()->flash('error', 'Anda Sedang Melakukan Cuti');
            return;
        }

        $jadwal = Jadwal::where('user_id', Auth::user()->id)->first();
        if ($jadwal) {
            $kehadiran = Kehadiran::where('user_id', Auth::user()->id)
                ->whereDate('created_at', date('Y-m-d'))->first();
            if (!$kehadiran) {
                $kehadiran = Kehadiran::create([
                    'user_id' => Auth::user()->id,
                    'jadwal_latitude' => $jadwal->office->latitude,
                    'jadwal_longitude' => $jadwal->office->longitude,
                    'jadwal_waktu_mulai' => $jadwal->shift->mulai_waktu,
                    'jadwal_waktu_akhir' => $jadwal->shift->akhir_waktu,
                    'start_latitude' => $this->latitude,
                    'start_longitude' => $this->longitude,
                    'start_time' => Carbon::now()->toTimeString(),
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
            } else {
                $kehadiran->update([
                    'end_latitude' => $this->latitude,
                    'end_longitude' => $this->longitude,
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
            }
        }

        return redirect('admin/kehadirans');
        // return redirect()->route('presensi', [
        //     'jadwal' => $jadwal,
        //     'insideRadius' => $this->insideRadius,
        // ]);
    }
}
