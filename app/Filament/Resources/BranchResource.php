<?php

namespace App\Filament\Resources;

use App\Enums\BranchType;
use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    public static function getNavigationIcon(): string|null { return 'heroicon-o-building-office'; }

    public static function getNavigationGroup(): string|null { return 'Master Data'; }
    public static function getNavigationSort(): int { return 3; }

    protected static ?string $navigationLabel = 'Cabang';

    protected static ?string $label = 'Cabang';

    protected static ?string $pluralLabel = 'Cabang';

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'super_admin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Informasi Cabang')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('code')
                            ->label('Kode')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options(BranchType::class)
                            ->required()
                            ->default('branch'),
                        Forms\Components\Select::make('parent_id')
                            ->label('Induk')
                            ->relationship('parent', 'name'),
                    ])->columns(2),
                Schemas\Components\Section::make('Kontak & Alamat')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->label('Kota')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('province')
                            ->label('Provinsi')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telepon')
                            ->maxLength(30),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(100),
                    ])->columns(2),
                Schemas\Components\Section::make('Rekening Bank')
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')
                            ->label('Nama Bank')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('bank_account')
                            ->label('Nomor Rekening')
                            ->maxLength(30),
                        Forms\Components\TextInput::make('bank_holder')
                            ->label('Atas Nama')
                            ->maxLength(100),
                    ])->columns(3),
                Schemas\Components\Section::make('Kuota & Komisi')
                    ->schema([
                        Forms\Components\TextInput::make('quota_haji')
                            ->label('Kuota Haji')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('quota_umrah')
                            ->label('Kuota Umrah')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('quota_used')
                            ->label('Kuota Terpakai')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('referral_commission_pct')
                            ->label('Komisi Referral (%)')
                            ->numeric()
                            ->suffix('%')
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Kota')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quota_haji')
                    ->label('Kuota Haji')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quota_umrah')
                    ->label('Kuota Umrah')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
