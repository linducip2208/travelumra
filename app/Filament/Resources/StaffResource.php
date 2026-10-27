<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class StaffResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationIcon(): string|null { return 'heroicon-o-users'; }

    public static function getNavigationGroup(): string|null { return 'Master Data'; }
    public static function getNavigationSort(): int { return 1; }

    protected static ?string $navigationLabel = 'Staff';

    protected static ?string $label = 'Staff';

    protected static ?string $pluralLabel = 'Staff';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Informasi Staff')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])->columns(2),
                Schemas\Components\Section::make('Role & Cabang')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options([
                                'super_admin' => 'Super Admin',
                                'admin' => 'Admin',
                                'finance' => 'Finance',
                                'branch_manager' => 'Branch Manager',
                                'agent' => 'Agent',
                                'muthawif' => 'Muthawif',
                            ])
                            ->required()
                            ->default('agent'),
                        Forms\Components\Select::make('branch_id')
                            ->label('Cabang')
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telepon')
                            ->maxLength(30),
                        Forms\Components\FileUpload::make('photo')
                            ->label('Foto')
                            ->image()
                            ->directory('staff-photos'),
                    ])->columns(2),
                Schemas\Components\Section::make('Muthawif')
                    ->schema([
                        Forms\Components\Toggle::make('is_muthawif')
                            ->label('Muthawif')
                            ->default(false),
                        Forms\Components\TextInput::make('muthawif_certification')
                            ->label('Sertifikasi')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('muthawif_experience_years')
                            ->label('Pengalaman (Tahun)')
                            ->numeric()
                            ->default(0),
                    ])->columns(3),
                Schemas\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_muthawif')
                    ->label('Muthawif')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin',
                        'finance' => 'Finance',
                        'branch_manager' => 'Branch Manager',
                        'agent' => 'Agent',
                        'muthawif' => 'Muthawif',
                    ]),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->relationship('branch', 'name'),
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
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }
}
