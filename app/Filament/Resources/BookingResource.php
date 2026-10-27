<?php

namespace App\Filament\Resources;

use App\Enums\BookingStatus;
use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Pilgrim;
use Filament\Forms;
use Filament\Schemas;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-ticket'; }
    public static function getNavigationGroup(): string|null { return 'Penjualan'; }
    public static function getNavigationSort(): int { return 1; }
    protected static ?string $navigationLabel = 'Booking';
    protected static ?string $label = 'Booking';
    protected static ?string $pluralLabel = 'Booking';

    public static function canAccess(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Data Booking')
                    ->schema([
                        Forms\Components\TextInput::make('booking_number')
                            ->label('No. Booking')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'BK-' . date('Ymd') . '-' . strtoupper(Str::random(4))),
                        Forms\Components\Select::make('package_id')
                            ->label('Paket')
                            ->relationship('package', 'name')
                            ->searchable()->preload()->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $pkg = Package::find($state);
                                if ($pkg) {
                                    $set('package_price', $pkg->price_quad);
                                }
                            }),
                        Forms\Components\Select::make('pilgrim_id')
                            ->label('Jemaah')
                            ->relationship('pilgrim', 'full_name')
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('room_type')
                            ->label('Tipe Kamar')
                            ->options(['single' => 'Single', 'double' => 'Double', 'triple' => 'Triple', 'quad' => 'Quad'])
                            ->default('quad')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $pkg = Package::find($get('package_id'));
                                if ($pkg) {
                                    $priceField = 'price_' . $state;
                                    $set('package_price', $pkg->$priceField ?? $pkg->price_quad);
                                    $total = ($pkg->$priceField ?? $pkg->price_quad) + ($get('additional_cost') ?? 0) - ($get('discount') ?? 0);
                                    $set('total_price', max(0, $total));
                                }
                            }),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(BookingStatus::class)
                            ->required()
                            ->default('pending'),
                    ])->columns(2),
                Schemas\Components\Section::make('Harga')
                    ->schema([
                        Forms\Components\TextInput::make('package_price')->label('Harga Paket')->numeric()->prefix('Rp')->required(),
                        Forms\Components\TextInput::make('additional_cost')->label('Biaya Tambahan')->numeric()->prefix('Rp')->default(0),
                        Forms\Components\TextInput::make('discount')->label('Diskon')->numeric()->prefix('Rp')->default(0),
                        Forms\Components\TextInput::make('total_price')->label('Total')->numeric()->prefix('Rp')->required(),
                    ])->columns(2),
                Schemas\Components\Section::make('Pembayaran & Referral')
                    ->schema([
                        Forms\Components\TextInput::make('paid_amount')->label('Sudah Dibayar')->numeric()->prefix('Rp')->default(0)->disabled()->dehydrated(false),
                        Forms\Components\TextInput::make('remaining_amount')->label('Sisa')->numeric()->prefix('Rp')->disabled()->dehydrated(false),
                        Forms\Components\Select::make('referral_by')->label('Referral Dari')->relationship('referralBy', 'full_name')->searchable(),
                        Forms\Components\TextInput::make('referral_commission')->label('Komisi Referral')->numeric()->prefix('Rp')->default(0),
                    ])->columns(2),
                Schemas\Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Forms\Components\Select::make('agent_id')->label('Agent')->relationship('agent', 'name')->searchable()->preload(),
                        Forms\Components\Select::make('branch_id')->label('Cabang')->relationship('branch', 'name')->searchable()->preload(),
                        Forms\Components\DatePicker::make('confirmed_date')->label('Tgl Konfirmasi'),
                        Forms\Components\DatePicker::make('cancelled_date')->label('Tgl Pembatalan'),
                        Forms\Components\Textarea::make('cancellation_reason')->label('Alasan Pembatalan')->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')->label('Catatan')->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')->label('No. Booking')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('pilgrim.full_name')->label('Jemaah')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('package.name')->label('Paket')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('room_type')->label('Kamar')->badge()->sortable(),
                Tables\Columns\TextColumn::make('total_price')->label('Total')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')->label('Dibayar')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('remaining_amount')->label('Sisa')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray', 'dp_paid' => 'warning', 'confirmed' => 'info',
                        'documents_complete' => 'info', 'visa_processing' => 'warning',
                        'visa_approved' => 'success', 'visa_rejected' => 'danger',
                        'ready_to_depart' => 'success', 'departed' => 'primary',
                        'returned' => 'success', 'cancelled' => 'danger', 'refunded' => 'danger',
                        default => 'gray',
                    })->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')->options(BookingStatus::class),
                Tables\Filters\SelectFilter::make('package_id')->label('Paket')->relationship('package', 'name'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([\Filament\Actions\EditAction::make(), \Filament\Actions\DeleteAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
