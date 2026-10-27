<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InsuranceResource\Pages;
use App\Models\Insurance;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class InsuranceResource extends Resource
{
    protected static ?string $model = Insurance::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-shield-check'; }
    public static function getNavigationGroup(): string|null { return 'Operasional'; }
    public static function getNavigationSort(): int { return 8; }
    protected static ?string $navigationLabel = 'Asuransi';
    protected static ?string $label = 'Asuransi';
    protected static ?string $pluralLabel = 'Asuransi';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'finance']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Polis Asuransi')->schema([
                TextInput::make('policy_number')->label('No. Polis')->required()->maxLength(50)->unique(ignoreRecord: true),
                Select::make('booking_id')->label('Booking')->relationship('booking', 'booking_number')->searchable()->preload()->required(),
                TextInput::make('provider')->label('Provider')->required()->maxLength(100),
                Select::make('coverage_type')->label('Jenis Coverage')->options([
                    'travel' => 'Perjalanan', 'health' => 'Kesehatan', 'baggage' => 'Bagasi',
                    'cancellation' => 'Pembatalan', 'comprehensive' => 'Komprehensif',
                ])->required(),
                TextInput::make('premium')->label('Premi (Rp)')->numeric()->prefix('Rp')->required(),
                TextInput::make('coverage_amount')->label('Nilai Pertanggungan (Rp)')->numeric()->prefix('Rp')->required(),
                DatePicker::make('start_date')->label('Mulai')->required(),
                DatePicker::make('end_date')->label('Berakhir')->required(),
                Select::make('status')->label('Status')->options([
                    'active' => 'Aktif', 'expired' => 'Expired', 'claimed' => 'Klaim', 'cancelled' => 'Dibatalkan',
                ])->default('active')->required(),
                Textarea::make('notes')->label('Catatan')->columnSpanFull(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('policy_number')->label('No. Polis')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('provider')->label('Provider')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('booking.booking_number')->label('Booking')->sortable(),
            Tables\Columns\TextColumn::make('coverage_type')->label('Jenis')->badge(),
            Tables\Columns\TextColumn::make('premium')->label('Premi')->money('IDR')->sortable(),
            Tables\Columns\TextColumn::make('coverage_amount')->label('Pertanggungan')->money('IDR'),
            Tables\Columns\TextColumn::make('start_date')->label('Mulai')->date('d M Y'),
            Tables\Columns\TextColumn::make('status')->label('Status')->badge()->color(fn ($s) => match($s) {
                'active' => 'success', 'expired' => 'gray', 'claimed' => 'warning', 'cancelled' => 'danger',
                default => 'gray',
            }),
        ])->defaultSort('created_at', 'desc')
        ->actions([Actions\EditAction::make(), Actions\DeleteAction::make()])
        ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInsurances::route('/'),
            'create' => Pages\CreateInsurance::route('/create'),
            'edit' => Pages\EditInsurance::route('/{record}/edit'),
        ];
    }
}
