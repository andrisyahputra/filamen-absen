<?php

use App\Livewire\Presensi;
use Illuminate\Support\Facades\Route;
use App\Exports\KehadiranExport;

Route::group(['middleware' => 'auth'], function () {
    Route::get('presensi', Presensi::class)->name('presensi');
    Route::get('kehadiran/export/', function () {
        return Excel::download(new KehadiranExport, 'kehadiran.xlsx');
    })->name('kehadiran-export');
});

Route::get('/login', function () {
    return redirect('admin/login');
})->name('login');


Route::get('/', function () {
    return view('welcome');
});
