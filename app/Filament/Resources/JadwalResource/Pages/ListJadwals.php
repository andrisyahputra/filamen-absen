<?php

namespace App\Filament\Resources\JadwalResource\Pages;

use App\Filament\Resources\JadwalResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListJadwals extends ListRecords
{
    protected static string $resource = JadwalResource::class;

    protected function getHeaderActions(): array
    {
        return [
// buat tombol

            Actions\CreateAction::make(),
        ];
    }
}
