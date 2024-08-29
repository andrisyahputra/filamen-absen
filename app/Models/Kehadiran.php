<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kehadiran extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function cekTelat(){
        $jadwal_mulai = Carbon::parse($this->jadwal_waktu_mulai);
        $jadwal_masuk = Carbon::parse($this->start_time);

        return $jadwal_masuk->greaterThan($jadwal_mulai);
    }

    public function durasiKerja(){
        $waktu_masuk = Carbon::parse($this->start_time);
        $waktu_pulang = Carbon::parse($this->end_time);

        $durasi = $waktu_masuk->diff($waktu_pulang);
        $jam = $durasi->h;
        $menit = $durasi->i;

        return "{$jam} jam {$menit} menit";
    }
}
