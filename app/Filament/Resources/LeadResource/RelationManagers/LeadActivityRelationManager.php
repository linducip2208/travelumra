<?php

namespace App\Filament\Resources\LeadResource\RelationManagers;

use App\Enums\ActivityType;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class LeadActivityRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';
    protected static ?string $title = 'Aktivitas';
    protected static ?string $label = 'Aktivitas';
    protected static ?string $pluralLabel = 'Aktivitas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Tipe Aktivitas')
                    ->options(ActivityType::class)
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('outcome')
                    ->label('Hasil')
                    ->maxLength(200),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->label('Tipe')->badge()->sortable(),
                Tables\Columns\TextColumn::make('description')->label('Deskripsi')->limit(50)->searchable(),
                Tables\Columns\TextColumn::make('outcome')->label('Hasil')->limit(30),
                Tables\Columns\TextColumn::make('user.name')->label('Oleh')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ]);
    }
}
