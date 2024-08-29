<?php

namespace App\Filament\Resources\CutiResource\Pages;

use App\Filament\Resources\CutiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateCuti extends CreateRecord
{
    protected static string $resource = CutiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array {
        $data['user_id'] = Auth::user()->id;
        $data['status'] = 'pending';
        return $data;
    }
}
