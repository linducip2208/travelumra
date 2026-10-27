<?php namespace App\Filament\Resources\ItineraryDayResource\Pages; use App\Filament\Resources\ItineraryDayResource; use Filament\Actions; use Filament\Resources\Pages\EditRecord;
class EditItineraryDay extends EditRecord { protected static string $resource = ItineraryDayResource::class; protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; } }
