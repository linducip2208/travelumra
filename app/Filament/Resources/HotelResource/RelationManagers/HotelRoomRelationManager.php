<?php

namespace App\Filament\Resources\HotelResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class HotelRoomRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';
    protected static ?string $title = 'Kamar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('room_number')
                    ->label('Nomor Kamar')
                    ->required()
                    ->maxLength(20),
                Forms\Components\Select::make('type')
                    ->label('Tipe Kamar')
                    ->options(\App\Enums\RoomType::class)
                    ->required(),
                Forms\Components\TextInput::make('floor')
                    ->label('Lantai')
                    ->numeric(),
                Forms\Components\TextInput::make('block')
                    ->label('Blok')
                    ->maxLength(20),
                Forms\Components\Toggle::make('is_available')
                    ->label('Tersedia')
                    ->default(true),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('room_number')->label('Nomor Kamar')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Tipe')->badge()->sortable(),
                Tables\Columns\TextColumn::make('floor')->label('Lantai')->sortable(),
                Tables\Columns\TextColumn::make('block')->label('Blok')->sortable(),
                Tables\Columns\IconColumn::make('is_available')->label('Tersedia')->boolean(),
            ])
            ->defaultSort('room_number')
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
