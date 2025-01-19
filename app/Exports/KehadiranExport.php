<?php

namespace App\Exports;

use App\Models\Kehadiran;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KehadiranExport implements FromQuery, WithHeadings
{
    public function query()
    {
        return Kehadiran::query()->join('users', 'kehadirans.user_id', '=', 'users.id')
            ->select([
                'users.email',
                'users.name as username',
                'kehadirans.jadwal_waktu_mulai',
                'kehadirans.jadwal_waktu_akhir'
            ]);
    }
    public function headings(): array
    {
        return ([
            'Email',
            'Username',
            'Jadwal Mulai',
            'Jadwal Akhir'
        ]);
    }
}