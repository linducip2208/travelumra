<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-document-text'; }
    public static function getNavigationGroup(): string|null { return 'Keuangan'; }
    public static function getNavigationSort(): int { return 1; }
    protected static ?string $navigationLabel = 'Invoice';
    protected static ?string $label = 'Invoice';
    protected static ?string $pluralLabel = 'Invoice';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'finance']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Data Invoice')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('No. Invoice')->required()->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4))),
                        Forms\Components\Select::make('booking_id')
                            ->label('Booking')->relationship('booking', 'booking_number')
                            ->searchable()->preload()->required()->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $booking = \App\Models\Booking::find($state);
                                if ($booking) {
                                    $set('pilgrim_id', $booking->pilgrim_id);
                                    $set('total_amount', $booking->remaining_amount);
                                }
                            }),
                        Forms\Components\Select::make('pilgrim_id')
                            ->label('Jemaah')->relationship('pilgrim', 'full_name')
                            ->searchable()->required()->disabled()->dehydrated(),
                        Forms\Components\Select::make('status')
                            ->label('Status')->options(InvoiceStatus::class)
                            ->required()->default('unpaid'),
                    ])->columns(2),
                Schemas\Components\Section::make('Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')->label('Total')->numeric()->prefix('Rp')->required(),
                        Forms\Components\TextInput::make('paid_amount')->label('Dibayar')->numeric()->prefix('Rp')->default(0)->disabled()->dehydrated(false),
                        Forms\Components\TextInput::make('remaining_amount')->label('Sisa')->numeric()->prefix('Rp')->disabled()->dehydrated(false),
                        Forms\Components\DatePicker::make('due_date')->label('Jatuh Tempo')->required(),
                        Forms\Components\TextInput::make('payment_link_url')->label('URL Pembayaran')->url()->maxLength(200),
                        Forms\Components\Textarea::make('notes')->label('Catatan')->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')->label('No. Invoice')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('booking.booking_number')->label('No. Booking')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('booking.package.name')->label('Paket')->searchable()->limit(30),
                Tables\Columns\TextColumn::make('pilgrim.full_name')->label('Jemaah')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->label('Total')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')->label('Dibayar')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('remaining_amount')->label('Sisa')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unpaid' => 'danger', 'partial' => 'warning',
                        'paid' => 'success', 'overdue' => 'danger', 'cancelled' => 'gray',
                        default => 'gray',
                    })->sortable(),
                Tables\Columns\TextColumn::make('due_date')->label('Jatuh Tempo')->date('d M Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')->options(InvoiceStatus::class),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                \Filament\Actions\EditAction::make(),
                Actions\Action::make('download_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->url(fn (Invoice $record) => route('invoice.pdf', $record))
                    ->openUrlInNewTab(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
