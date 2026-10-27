<?php

namespace App\Filament\Resources;

use App\Enums\PaymentStatus;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-banknotes'; }
    public static function getNavigationGroup(): string|null { return 'Keuangan'; }
    public static function getNavigationSort(): int { return 2; }
    protected static ?string $navigationLabel = 'Pembayaran';
    protected static ?string $label = 'Pembayaran';
    protected static ?string $pluralLabel = 'Pembayaran';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'finance']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Data Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('payment_number')
                            ->label('No. Pembayaran')->required()->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(4))),
                        Forms\Components\Select::make('invoice_id')
                            ->label('Invoice')->relationship('invoice', 'invoice_number')
                            ->searchable()->preload()->required()->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $inv = \App\Models\Invoice::find($state);
                                if ($inv) {
                                    $set('booking_id', $inv->booking_id);
                                }
                            }),
                        Forms\Components\Select::make('booking_id')
                            ->label('Booking')->relationship('booking', 'booking_number')
                            ->searchable()->required()->disabled()->dehydrated(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah')->numeric()->prefix('Rp')->required(),
                        Forms\Components\TextInput::make('payment_method')
                            ->label('Metode Pembayaran')->maxLength(30),
                        Forms\Components\Select::make('status')
                            ->label('Status')->options(PaymentStatus::class)
                            ->required()->default('pending'),
                    ])->columns(2),
                Schemas\Components\Section::make('Bank & Bukti')
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')->label('Nama Bank')->maxLength(50),
                        Forms\Components\TextInput::make('bank_account')->label('No. Rekening')->maxLength(30),
                        Forms\Components\TextInput::make('bank_holder')->label('Atas Nama')->maxLength(100),
                        Forms\Components\DatePicker::make('payment_date')->label('Tanggal Bayar')->required(),
                        Forms\Components\FileUpload::make('proof_file')->label('Bukti Pembayaran')->image()->directory('payments')->disk('local')->visibility('private'),
                        Forms\Components\Textarea::make('rejection_reason')->label('Alasan Penolakan')->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')->label('Catatan')->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_number')->label('No. Pembayaran')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('invoice.invoice_number')->label('No. Invoice')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('booking.booking_number')->label('No. Booking')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('amount')->label('Jumlah')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('payment_method')->label('Metode')->sortable(),
                Tables\Columns\TextColumn::make('payment_date')->label('Tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning', 'confirmed' => 'success',
                        'rejected' => 'danger', 'refunded' => 'info',
                        default => 'gray',
                    })->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')->options(PaymentStatus::class),
                Tables\Filters\SelectFilter::make('payment_method')->label('Metode')->options(
                    fn () => Payment::distinct()->pluck('payment_method', 'payment_method')->filter()->toArray()
                ),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
