<?php

namespace App\Filament\Resources\ClusterResource\RelationManagers;

use App\Models\Booking;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class ClusterPilgrimRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';
    protected static ?string $title = 'Jemaah Kloter';
    protected static ?string $label = 'Jemaah';
    protected static ?string $pluralLabel = 'Jemaah';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('booking_id')
                    ->label('Booking')
                    ->options(Booking::pluck('booking_number', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('seat_number')
                    ->label('Nomor Kursi')
                    ->maxLength(10),
                Forms\Components\TextInput::make('room_number')
                    ->label('Nomor Kamar')
                    ->maxLength(10),
                Forms\Components\TextInput::make('catering_group')
                    ->label('Grup Katering')
                    ->maxLength(20),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')->label('No. Booking')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('pilgrim.full_name')->label('Nama Jemaah')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('pivot.seat_number')->label('Kursi'),
                Tables\Columns\TextColumn::make('pivot.room_number')->label('Kamar'),
                Tables\Columns\TextColumn::make('pivot.catering_group')->label('Grup Katering'),
            ])
            ->defaultSort('booking_number')
            ->headerActions([
                Actions\AttachAction::make()
                    ->form(fn (Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('seat_number')->label('Nomor Kursi')->maxLength(10),
                        Forms\Components\TextInput::make('room_number')->label('Nomor Kamar')->maxLength(10),
                        Forms\Components\TextInput::make('catering_group')->label('Grup Katering')->maxLength(20),
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
