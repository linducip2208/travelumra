<?php namespace App\Filament\Resources\ItineraryDayResource\Pages; use App\Filament\Resources\ItineraryDayResource; use Filament\Actions; use Filament\Resources\Pages\ListRecords;
class ListItineraryDays extends ListRecords { protected static string $resource = ItineraryDayResource::class; protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; } }
