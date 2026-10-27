<?php
namespace App\Filament\Resources\PilgrimResource\Pages;
use App\Filament\Resources\PilgrimResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPilgrims extends ListRecords
{
    protected static string $resource = PilgrimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
