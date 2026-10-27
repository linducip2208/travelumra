<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use App\Models\ExpenseCategory;
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

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-arrow-trending-down'; }
    public static function getNavigationGroup(): string|null { return 'Keuangan'; }
    public static function getNavigationSort(): int { return 3; }
    protected static ?string $navigationLabel = 'Pengeluaran';
    protected static ?string $label = 'Pengeluaran';
    protected static ?string $pluralLabel = 'Pengeluaran';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'finance']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Pengeluaran')
                    ->schema([
                        TextInput::make('expense_number')
                            ->label('No. Pengeluaran')
                            ->default(fn () => Expense::generateExpenseNumber())
                            ->disabled()
                            ->dehydrated(),
                        Select::make('expense_category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        TextInput::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('amount')
                            ->label('Jumlah (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        DatePicker::make('expense_date')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),
                    ])->columns(2),
                Section::make('Detail')
                    ->schema([
                        Select::make('branch_id')
                            ->label('Cabang')
                            ->relationship('branch', 'name')
                            ->preload()
                            ->searchable(),
                        Select::make('booking_id')
                            ->label('Booking Terkait')
                            ->relationship('booking', 'booking_number')
                            ->searchable()
                            ->nullable(),
                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'transfer' => 'Transfer Bank',
                                'cash' => 'Tunai',
                                'virtual_account' => 'Virtual Account',
                            ])
                            ->default('transfer'),
                        TextInput::make('recipient')
                            ->label('Penerima')
                            ->maxLength(100),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'pending' => 'Menunggu Approval',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->default('approved')
                            ->required(),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expense_number')->label('No. EXP')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori')->badge()->sortable(),
                Tables\Columns\TextColumn::make('description')->label('Deskripsi')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('amount')->label('Jumlah')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('expense_date')->label('Tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()->color(fn ($state) => match($state) {
                    'approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger', 'draft' => 'gray',
                    default => 'gray',
                }),
                Tables\Columns\TextColumn::make('branch.name')->label('Cabang')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('creator.name')->label('Dibuat Oleh')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('expense_category_id')->label('Kategori')->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('status')->label('Status')->options([
                    'draft' => 'Draft', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected',
                ]),
                Tables\Filters\SelectFilter::make('branch_id')->label('Cabang')->relationship('branch', 'name'),
            ])
            ->defaultSort('expense_date', 'desc')
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
