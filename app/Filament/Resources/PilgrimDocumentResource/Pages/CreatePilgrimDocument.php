<?php

namespace App\Filament\Resources\PilgrimDocumentResource\Pages;

use App\Filament\Resources\PilgrimDocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePilgrimDocument extends CreateRecord
{
    protected static string $resource = PilgrimDocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
