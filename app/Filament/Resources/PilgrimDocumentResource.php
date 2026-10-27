<?php

namespace App\Filament\Resources;

use App\Enums\DocumentType;
use App\Filament\Resources\PilgrimDocumentResource\Pages;
use App\Models\PilgrimDocument;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PilgrimDocumentResource extends Resource
{
    protected static ?string $model = PilgrimDocument::class;

    public static function getNavigationIcon(): string|null { return 'heroicon-o-document-text'; }

    public static function getNavigationGroup(): string|null { return 'Dokumen'; }
    public static function getNavigationSort(): int { return 1; }

    protected static ?string $navigationLabel = 'Dokumen Jamaah';

    protected static ?string $modelLabel = 'Dokumen';

    protected static ?string $pluralModelLabel = 'Dokumen Jamaah';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Dokumen')
                    ->schema([
                        Select::make('pilgrim_id')
                            ->label('Jamaah')
                            ->relationship('pilgrim', 'full_name')
                            ->searchable()
                            ->required(),
                        Select::make('type')
                            ->label('Tipe Dokumen')
                            ->options(DocumentType::class)
                            ->required(),
                        FileUpload::make('file_path')
                            ->label('File')
                            ->directory('pilgrim-documents')
                            ->disk('local')
                            ->visibility('private')
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Menunggu',
                                'verified' => 'Terverifikasi',
                                'rejected' => 'Ditolak',
                            ])
                            ->required()
                            ->default('pending'),
                        Textarea::make('verification_notes')
                            ->label('Catatan Verifikasi')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pilgrim.full_name')
                    ->label('Jamaah')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge(),
                TextColumn::make('file_path')
                    ->label('File')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'verified',
                        'danger' => 'rejected',
                    ]),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options(DocumentType::class),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('pilgrim_id')
                    ->label('Jamaah')
                    ->relationship('pilgrim', 'full_name'),
            ])
            ->actions([
                Actions\Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(fn (PilgrimDocument $record) => $record->update(['status' => 'verified']))
                    ->requiresConfirmation()
                    ->visible(fn (PilgrimDocument $record) => $record->status === 'pending'),
                Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('verification_notes')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(function (PilgrimDocument $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'verification_notes' => $data['verification_notes'],
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (PilgrimDocument $record) => $record->status === 'pending'),
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPilgrimDocuments::route('/'),
            'create' => Pages\CreatePilgrimDocument::route('/create'),
            'edit' => Pages\EditPilgrimDocument::route('/{record}/edit'),
        ];
    }
}
