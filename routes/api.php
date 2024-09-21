<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthControler;
use App\Http\Controllers\Api\KehadiranControler;

Route::post('/login', [AuthControler::class, 'login'])->name('login');

Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('/get-kehadiran', [KehadiranControler::class, 'getkehadiran'])->name('get-kehadiran');
    Route::get('/get-jadwal', [KehadiranControler::class, 'getJadwal']);
    Route::post('/store-kehadiran', [KehadiranControler::class, 'store']);
    Route::get('/get-kehadiran-by-bulan-tahun/{bulan}/{tahun}', [KehadiranControler::class, 'getkehadiranByBulanDantahun']);
    Route::get('/get-gambar', [KehadiranControler::class, 'getGambar']);
    Route::post('/banned', [KehadiranControler::class, 'banned']);

});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
