<?php
namespace App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! isset($data['agent_id']) && auth()->user()->role === 'agent') {
            $data['agent_id'] = auth()->id();
        }

        return $data;
    }

    protected function mutateFormDataAfterFill(array $data): array
    {
        $pilgrimId = request()->query('pilgrim_id');
        if ($pilgrimId) {
            $data['pilgrim_id'] = $pilgrimId;
        }

        return $data;
    }
}
