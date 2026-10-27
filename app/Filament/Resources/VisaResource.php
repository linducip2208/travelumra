<?php

namespace App\Filament\Resources;

use App\Enums\VisaStatus;
use App\Enums\VisaType;
use App\Filament\Resources\VisaResource\Pages;
use App\Models\Booking;
use App\Models\Pilgrim;
use App\Models\Visa;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class VisaResource extends Resource
{
    protected static ?string $model = Visa::class;

    public static function getNavigationIcon(): string|null { return 'heroicon-o-identification'; }

    public static function getNavigationGroup(): string|null { return 'Operasional'; }
    public static function getNavigationSort(): int { return 6; }

    protected static ?string $navigationLabel = 'Visa';

    protected static ?string $label = 'Visa';

    protected static ?string $pluralLabel = 'Visa';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'branch_manager']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Data Jemaah & Booking')
                    ->schema([
                        Forms\Components\Select::make('pilgrim_id')
                            ->label('Jemaah')
                            ->relationship('pilgrim', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('booking_id', null)),
                        Forms\Components\Select::make('booking_id')
                            ->label('Booking')
                            ->options(fn (callable $get) => $get('pilgrim_id')
                                ? Booking::where('pilgrim_id', $get('pilgrim_id'))->pluck('booking_number', 'id')
                                : [])
                            ->searchable()
                            ->required(),
                    ])->columns(2),
                Schemas\Components\Section::make('Detail Visa')
                    ->schema([
                        Forms\Components\TextInput::make('visa_number')
                            ->label('Nomor Visa')
                            ->maxLength(50),
                        Forms\Components\Select::make('type')
                            ->label('Tipe Visa')
                            ->options(VisaType::class)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(VisaStatus::class)
                            ->required()
                            ->default('not_submitted'),
                        Forms\Components\DatePicker::make('submission_date')
                            ->label('Tanggal Pengajuan'),
                        Forms\Components\DatePicker::make('approval_date')
                            ->label('Tanggal Persetujuan'),
                        Forms\Components\DatePicker::make('issue_date')
                            ->label('Tanggal Terbit'),
                        Forms\Components\DatePicker::make('expiry_date')
                            ->label('Tanggal Kadaluarsa'),
                        Forms\Components\TextInput::make('embassy')
                            ->label('Kedutaan')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('mofa_number')
                            ->label('Nomor MOFA')
                            ->maxLength(50),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pilgrim.full_name')
                    ->label('Jemaah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('booking.booking_number')
                    ->label('No. Booking')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('visa_number')
                    ->label('Nomor Visa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'not_submitted' => 'gray',
                        'submitted' => 'warning',
                        'in_review' => 'info',
                        'approved' => 'success',
                        'issued' => 'success',
                        'rejected' => 'danger',
                        'expired' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('submission_date')
                    ->label('Tgl Pengajuan')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Tgl Kadaluarsa')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe Visa')
                    ->options(VisaType::class),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(VisaStatus::class),
                Tables\Filters\Filter::make('submission_date')
                    ->label('Tgl Pengajuan')
                    ->form([
                        Forms\Components\DatePicker::make('submission_from')
                            ->label('Dari'),
                        Forms\Components\DatePicker::make('submission_until')
                            ->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['submission_from'], fn ($q) => $q->whereDate('submission_date', '>=', $data['submission_from']))
                            ->when($data['submission_until'], fn ($q) => $q->whereDate('submission_date', '<=', $data['submission_until']));
                    }),
            ])
            ->defaultSort('created_at', 'desc')
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisas::route('/'),
            'create' => Pages\CreateVisa::route('/create'),
            'edit' => Pages\EditVisa::route('/{record}/edit'),
        ];
    }
}
