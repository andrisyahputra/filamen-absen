<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Jadwal;
use App\Models\Kehadiran;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class KehadiranControler extends Controller
{
    //
    public function getkehadiran()
    {
        $userId = auth()->user()->id;
        $sekarang = now()->toDateString();
        $bulanSekarang = now()->month;

        $kehadiranSekarang = Kehadiran::select('start_time', 'end_time')
            ->where('user_id', $userId)
            ->whereDate('created_at', $sekarang)
            ->first();
        $kehadiranPerBulan = Kehadiran::select('start_time', 'end_time', 'created_at')
            ->where('user_id', $userId)
            ->whereMonth('created_at', $bulanSekarang)
            ->get()
            ->map(function ($kehadiran) {
                return [
                    'start_time' => $kehadiran->start_time,
                    'end_time' => $kehadiran->end_time,
                    'date' => $kehadiran->created_at->toDateString(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'sekarang' => $kehadiranSekarang,
                'per_bulan' => $kehadiranPerBulan
            ],
            'message' => 'Berhasil',
        ], 200);
    }

    public function getJadwal()
    {
        $jadwal = Jadwal::with(['office', 'shift'])
            ->where('user_id', auth()->user()->id)
            ->first();

        $hari_ini = Carbon::today()->format('Y-m-d');
        $setujuCuti = Cuti::where('user_id', Auth::user()->id)
            ->where('status', 'setuju')
            ->whereDate('tgl_mulai', '<=', $hari_ini)
            ->whereDate('tgl_akhir', '>=', $hari_ini)
            ->exists();
        if ($setujuCuti) {
            return response()->json([
                'success' => true,
                'message' => 'Anda Tidak dapat melakukan absen karena sedang cuti',
                'data' => null,
            ], 200);
        }
        if ($jadwal->is_banned) {

            return response()->json([
                'success' => true,
                'message' => 'Anda tidak bisa absen sedang di banned',
                'data' => $jadwal,
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil',
                'data' => $jadwal,
            ], 200);
        }
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal',
                'data' => $validator->errors(),
            ], 422);
        }
        $hari_ini = Carbon::today()->format('Y-m-d');
        $setujuCuti = Cuti::where('user_id', Auth::user()->id)
            ->where('status', 'setuju')
            ->whereDate('tgl_mulai', '<=', $hari_ini)
            ->whereDate('tgl_akhir', '>=', $hari_ini)
            ->exists();
        if ($setujuCuti) {
            return response()->json([
                'success' => true,
                'message' => 'Anda Tidak dapat melakukan absen karena sedang cuti',
                'data' => null,
            ], 200);
        }

        $jadwal = Jadwal::where('user_id', Auth::user()->id)->first();
        if ($validator) {
            $kehadiran = Kehadiran::where('user_id', Auth::user()->id)
                ->whereDate('created_at', date('Y-m-d'))->first();
            if (!$kehadiran) {
                $kehadiran = Kehadiran::create([
                    'user_id' => Auth::user()->id,
                    'jadwal_latitude' => $jadwal->office->latitude,
                    'jadwal_longitude' => $jadwal->office->longitude,
                    'jadwal_waktu_mulai' => $jadwal->shift->mulai_waktu,
                    'jadwal_waktu_akhir' => $jadwal->shift->akhir_waktu,
                    'start_latitude' => $request->latitude,
                    'start_longitude' => $request->longitude,
                    'start_time' => Carbon::now()->toTimeString(),
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
            } else {
                $kehadiran->update([
                    'end_latitude' => $request->latitude,
                    'end_longitude' => $request->longitude,
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
            }


            return response()->json([
                'success' => true,
                'message' => 'Berhasil Hadir',
                'data' => $kehadiran,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada Kehadiran',
                'data' => null,
            ], 200);
        }

    }
    public function getkehadiranByBulanDantahun($bulan, $tahun)
    {
        $validator = Validator::make([
            'bulan' => $bulan,
            'tahun' => $tahun
        ], [
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2023|max:2024'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal',
                'data' => $validator->errors(),
            ], 422);
        }

        $kehadiranPerBulan = Kehadiran::select('start_time', 'end_time', 'created_at')
            ->where('user_id', Auth::user()->id)
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get()
            ->map(function ($kehadiran) {
                return [
                    'start_time' => $kehadiran->start_time,
                    'end_time' => $kehadiran->end_time,
                    'date' => $kehadiran->created_at->toDateString(),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Suksess',
            'data' => $kehadiranPerBulan,
        ], 200);
    }
     public function getGambar(){
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'message' => 'Suksess ambil gambar',
            'data' => $user->gambar_url,
        ], 200);

     }
      public function banned(){
        $jadwal = Jadwal::where('user_id', auth()->user()->id)
            ->first();
            if($jadwal){
                $jadwal->update([
                    'is_banned' => true,
                ]);
            }

        return response()->json([
            'success' => true,
            'message' => 'Suksess dibanned',
            'data' => $jadwal,
        ], 200);

    }
}
