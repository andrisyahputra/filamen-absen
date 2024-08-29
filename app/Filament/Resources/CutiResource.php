<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CutiResource\Pages;
use App\Filament\Resources\CutiResource\RelationManagers;
use App\Models\Cuti;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CutiResource extends Resource
{
    protected static ?string $model = Cuti::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $schema = [
  Forms\Components\Section::make('Detail')
                ->schema([
                    Forms\Components\DatePicker::make('tgl_mulai')
                        ->required(),
                    Forms\Components\DatePicker::make('tgl_akhir')
                        ->required(),
                    Forms\Components\Textarea::make('alasan')
                        ->required()
                        ->columnSpanFull(),

                ]),
                ];
        if(Auth::user()->hasrole('super_admin')){
            $schema[] = Forms\Components\Section::make('Persetujuan')
                ->schema([
                    Forms\Components\Select::make('status')
                    ->options([
                        'setuju' => 'setuju',
                        'tolak' => 'tolak'
                    ])
                    ,
                        // ->required(),
                    Forms\Components\Textarea::make('catatan')
                        ->columnSpanFull(),
                ]);
        }
        return $form->schema($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $is_super_admin = Auth::user()->hasRole('super_admin');
                // dd($is_super_admin);
                if (!$is_super_admin) {
                    $query->where('user_id', Auth::user()->id);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_akhir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'setuju' => 'success',
                        'tolak' => 'danger',
                    })
                    ->description(fn(Cuti $record): string => $record->catatan ?? ''),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCutis::route('/'),
            'create' => Pages\CreateCuti::route('/create'),
            'edit' => Pages\EditCuti::route('/{record}/edit'),
        ];
    }
}
