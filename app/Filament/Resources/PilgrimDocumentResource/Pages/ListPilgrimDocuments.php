<?php

namespace App\Filament\Resources\PilgrimDocumentResource\Pages;

use App\Filament\Resources\PilgrimDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPilgrimDocuments extends ListRecords
{
    protected static string $resource = PilgrimDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
