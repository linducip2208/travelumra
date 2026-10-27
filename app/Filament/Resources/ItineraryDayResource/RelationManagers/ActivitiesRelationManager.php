<?php
namespace App\Filament\Resources\ItineraryDayResource\RelationManagers;
use Filament\Forms\Components\{TextInput, Textarea, Select};
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables; use Filament\Actions; use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';
    protected static ?string $title = 'Aktivitas';

    public function form(Schema $schema): Schema { return $schema->schema([
        TextInput::make('title')->label('Aktivitas')->required()->maxLength(255),
        TextInput::make('time')->label('Waktu')->maxLength(20)->placeholder('08:00 - 10:00'),
        Select::make('location_type')->label('Lokasi')->options([
            'makkah'=>'Makkah','madinah'=>'Madinah','jeddah'=>'Jeddah','mina'=>'Mina','arafah'=>'Arafah','muzdalifah'=>'Muzdalifah',
        ])->default('makkah'),
        Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
        TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
    ]); }

    public function table(Table $table): Table { return $table->columns([
        Tables\Columns\TextColumn::make('time')->label('Waktu'),
        Tables\Columns\TextColumn::make('title')->label('Aktivitas')->searchable(),
        Tables\Columns\TextColumn::make('location_type')->label('Lokasi')->badge(),
        Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
    ])->defaultSort('sort_order')
    ->actions([Actions\EditAction::make(),Actions\DeleteAction::make()]); }
}
