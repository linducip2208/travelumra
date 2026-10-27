<?php
namespace App\Filament\Resources;
use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use Filament\Forms\Components\{DatePicker, Select, TextInput, Toggle, Textarea};
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables; use Filament\Actions; use Filament\Tables\Table;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-ticket'; }
    public static function getNavigationGroup(): string|null { return 'Penjualan'; }
    public static function getNavigationSort(): int { return 3; }
    protected static ?string $navigationLabel = 'Kode Promo';
    protected static ?string $label = 'Kode Promo'; protected static ?string $pluralLabel = 'Kode Promo';

    public static function canAccess(): bool { return in_array(auth()->user()?->role, ['super_admin','admin']); }

    public static function form(Schema $schema): Schema { return $schema->schema([
        Section::make('Kode Promo')->schema([
            TextInput::make('code')->label('Kode')->required()->unique(ignoreRecord:true)->maxLength(20)->helperText('Contoh: UMRAH10, RAMADHAN2026'),
            Select::make('type')->label('Tipe')->options(['percentage'=>'Persentase (%)','fixed'=>'Nominal (Rp)'])->required()->default('percentage'),
            TextInput::make('value')->label('Nilai')->numeric()->required()->helperText(fn($get)=>$get('type')==='percentage'?'Persentase diskon (%)':'Nominal diskon (Rp)'),
            TextInput::make('min_booking_amount')->label('Min. Booking (Rp)')->numeric()->helperText('Booking minimal untuk menggunakan kode'),
            TextInput::make('max_discount')->label('Max. Diskon (Rp)')->numeric()->helperText('Batas maksimal potongan (untuk tipe persentase)'),
        ])->columns(3),
        Section::make('Pengaturan')->schema([
            TextInput::make('usage_limit')->label('Batas Pakai')->numeric(),
            DatePicker::make('start_date')->label('Mulai Berlaku'),
            DatePicker::make('end_date')->label('Berakhir'),
            Select::make('package_id')->label('Paket Khusus')->relationship('package','name')->searchable()->preload()->nullable()->helperText('Kosongkan untuk semua paket'),
            Toggle::make('is_active')->label('Aktif')->default(true),
        ])->columns(3),
    ]); }

    public static function table(Table $table): Table { return $table->columns([
        Tables\Columns\TextColumn::make('code')->label('Kode')->searchable()->sortable()->copyable(),
        Tables\Columns\TextColumn::make('type')->label('Tipe')->badge()->formatStateUsing(fn($s)=>$s==='percentage'?'%':'Rp'),
        Tables\Columns\TextColumn::make('value')->label('Nilai')->formatStateUsing(fn($s,$r)=>$r->type==='percentage'?$s.'%':'Rp '.number_format($s,0,',','.')),
        Tables\Columns\TextColumn::make('used_count')->label('Terpakai / Limit')
            ->formatStateUsing(fn($record) => $record->used_count . ' / ' . ($record->usage_limit ?? '∞'))
            ->sortable(),
        Tables\Columns\TextColumn::make('start_date')->label('Mulai')->date('d M Y'),
        Tables\Columns\TextColumn::make('end_date')->label('Berakhir')->date('d M Y'),
        Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
    ])->defaultSort('created_at','desc')
    ->actions([Actions\EditAction::make(),Actions\DeleteAction::make()])
    ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]); }

    public static function getRelations(): array { return []; }
    public static function getPages(): array { return [
        'index'=>Pages\ListPromoCodes::route('/'),
        'create'=>Pages\CreatePromoCode::route('/create'),
        'edit'=>Pages\EditPromoCode::route('/{record}/edit'),
    ]; }
}
