<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InsuranceClaimResource\Pages;
use App\Models\InsuranceClaim;
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

class InsuranceClaimResource extends Resource
{
    protected static ?string $model = InsuranceClaim::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-clipboard-document-check'; }
    public static function getNavigationGroup(): string|null { return 'Operasional'; }
    public static function getNavigationSort(): int { return 9; }
    protected static ?string $navigationLabel = 'Klaim Asuransi';
    protected static ?string $label = 'Klaim';
    protected static ?string $pluralLabel = 'Klaim Asuransi';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'finance']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Detail Klaim')->schema([
                TextInput::make('claim_number')->label('No. Klaim')->default(fn () => InsuranceClaim::generateClaimNumber())->disabled()->dehydrated(),
                Select::make('insurance_id')->label('Polis Asuransi')->relationship('insurance', 'policy_number')->searchable()->preload()->required(),
                Select::make('incident_type')->label('Jenis Insiden')->options([
                    'medical' => 'Medis', 'accident' => 'Kecelakaan', 'baggage_loss' => 'Kehilangan Bagasi',
                    'flight_delay' => 'Delay Penerbangan', 'trip_cancellation' => 'Pembatalan', 'other' => 'Lainnya',
                ])->required(),
                DatePicker::make('incident_date')->label('Tgl Kejadian')->required(),
                TextInput::make('claim_amount')->label('Nilai Klaim (Rp)')->numeric()->prefix('Rp')->required(),
                TextInput::make('approved_amount')->label('Disetujui (Rp)')->numeric()->prefix('Rp'),
                Select::make('status')->label('Status')->options([
                    'submitted' => 'Diajukan', 'reviewing' => 'Review', 'approved' => 'Disetujui',
                    'rejected' => 'Ditolak', 'paid' => 'Dibayarkan',
                ])->default('submitted')->required(),
                Select::make('handled_by')->label('Ditangani Oleh')->relationship('handler', 'name')->searchable()->preload(),
                Textarea::make('description')->label('Deskripsi Kejadian')->required()->columnSpanFull(),
                Textarea::make('resolution')->label('Resolusi')->columnSpanFull(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('claim_number')->label('No. Klaim')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('insurance.policy_number')->label('No. Polis')->searchable(),
            Tables\Columns\TextColumn::make('incident_type')->label('Jenis')->badge(),
            Tables\Columns\TextColumn::make('incident_date')->label('Tgl Kejadian')->date('d M Y')->sortable(),
            Tables\Columns\TextColumn::make('claim_amount')->label('Nilai Klaim')->money('IDR')->sortable(),
            Tables\Columns\TextColumn::make('status')->label('Status')->badge()->color(fn ($s) => match($s) {
                'submitted' => 'gray', 'reviewing' => 'warning', 'approved' => 'info',
                'paid' => 'success', 'rejected' => 'danger',
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
            'index' => Pages\ListInsuranceClaims::route('/'),
            'create' => Pages\CreateInsuranceClaim::route('/create'),
            'edit' => Pages\EditInsuranceClaim::route('/{record}/edit'),
        ];
    }
}
