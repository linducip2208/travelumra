<?php

namespace App\Filament\Resources;

use App\Enums\BroadcastChannel;
use App\Enums\BroadcastStatus;
use App\Filament\Resources\BroadcastResource\Pages;
use App\Models\Broadcast;
use App\Models\Cluster;
use App\Models\Pilgrim;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class BroadcastResource extends Resource
{
    protected static ?string $model = Broadcast::class;

    public static function getNavigationIcon(): string|null { return 'heroicon-o-megaphone'; }

    public static function getNavigationGroup(): string|null { return 'CRM'; }
    public static function getNavigationSort(): int { return 2; }

    protected static ?string $navigationLabel = 'Siaran';

    protected static ?string $label = 'Siaran';

    protected static ?string $pluralLabel = 'Siaran';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Informasi Siaran')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\RichEditor::make('message')
                            ->label('Pesan')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('channel')
                            ->label('Kanal')
                            ->options(BroadcastChannel::class)
                            ->required(),
                    ])->columns(2),
                Schemas\Components\Section::make('Penerima')
                    ->schema([
                        Forms\Components\Select::make('recipient_ids')
                            ->label('Penerima (Jemaah)')
                            ->multiple()
                            ->options(Pilgrim::pluck('full_name', 'id'))
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('cluster_id')
                            ->label('Kloter (Kirim ke seluruh jemaah kloter)')
                            ->options(Cluster::pluck('name', 'id'))
                            ->searchable(),
                    ])->columns(1),
                Schemas\Components\Section::make('Status & Jadwal')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(BroadcastStatus::class)
                            ->required()
                            ->default('draft'),
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Jadwal Kirim')
                            ->native(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('channel')
                    ->label('Kanal')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('recipient_count')
                    ->label('Total Penerima')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_count')
                    ->label('Terkirim')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->label('Kanal')
                    ->options(BroadcastChannel::class),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(BroadcastStatus::class),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Actions\Action::make('send_now')
                    ->label('Kirim Sekarang')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->action(fn (Broadcast $record) => $record->update([
                        'status' => 'scheduled',
                        'scheduled_at' => now(),
                    ]))
                    ->visible(fn (Broadcast $record): bool => !in_array($record->status, ['sent', 'failed'])),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBroadcasts::route('/'),
            'create' => Pages\CreateBroadcast::route('/create'),
            'edit' => Pages\EditBroadcast::route('/{record}/edit'),
        ];
    }
}
