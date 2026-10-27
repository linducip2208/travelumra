<?php

namespace App\Filament\Resources;

use App\Enums\MaritalStatus;
use App\Enums\PilgrimGender;
use App\Filament\Resources\PilgrimResource\Pages;
use App\Models\Branch;
use App\Models\Pilgrim;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class PilgrimResource extends Resource
{
    protected static ?string $model = Pilgrim::class;

    public static function getNavigationIcon(): string|null { return 'heroicon-o-user-circle'; }

    public static function getNavigationGroup(): string|null { return 'Master Data'; }
    public static function getNavigationSort(): int { return 2; }

    protected static ?string $navigationLabel = 'Jemaah';

    protected static ?string $label = 'Jemaah';

    protected static ?string $pluralLabel = 'Jemaah';

    public static function canAccess(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Data Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('registration_number')
                            ->label('Nomor Registrasi')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'PGR-' . date('Ymd') . '-' . str_pad(Pilgrim::count() + 1, 4, '0', STR_PAD_LEFT)),
                        Forms\Components\TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('title')
                            ->label('Gelar')
                            ->maxLength(20),
                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(PilgrimGender::class),
                        Forms\Components\TextInput::make('birth_place')
                            ->label('Tempat Lahir')
                            ->maxLength(50),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Tanggal Lahir'),
                        Forms\Components\Select::make('marital_status')
                            ->label('Status Nikah')
                            ->options(MaritalStatus::class),
                        Forms\Components\TextInput::make('blood_type')
                            ->label('Golongan Darah')
                            ->maxLength(3),
                    ])->columns(2),
                Schemas\Components\Section::make('Dokumen')
                    ->schema([
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK')
                            ->maxLength(16),
                        Forms\Components\TextInput::make('kk_number')
                            ->label('No. KK')
                            ->maxLength(16),
                        Forms\Components\TextInput::make('passport_number')
                            ->label('No. Paspor')
                            ->maxLength(20),
                        Forms\Components\DatePicker::make('passport_issued_date')
                            ->label('Tgl Terbit Paspor'),
                        Forms\Components\DatePicker::make('passport_expiry_date')
                            ->label('Tgl Kadaluarsa Paspor'),
                        Forms\Components\TextInput::make('passport_issuer')
                            ->label('Penerbit Paspor')
                            ->maxLength(50),
                    ])->columns(2),
                Schemas\Components\Section::make('Kontak & Alamat')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Telepon')
                            ->maxLength(30),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(100),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->label('Kota')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('province')
                            ->label('Provinsi')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Kode Pos')
                            ->maxLength(10),
                    ])->columns(2),
                Schemas\Components\Section::make('Kontak Darurat & Mahram')
                    ->schema([
                        Forms\Components\TextInput::make('emergency_name')
                            ->label('Nama Kontak Darurat')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('emergency_relation')
                            ->label('Hubungan')
                            ->maxLength(30),
                        Forms\Components\TextInput::make('emergency_phone')
                            ->label('Telepon Darurat')
                            ->maxLength(30),
                        Forms\Components\Select::make('mahram_id')
                            ->label('Mahram')
                            ->options(Pilgrim::where('gender', 'male')->pluck('full_name', 'id'))
                            ->searchable(),
                    ])->columns(2),
                Schemas\Components\Section::make('Kesehatan')
                    ->schema([
                        Forms\Components\Toggle::make('has_meningitis_vaccine')
                            ->label('Vaksin Meningitis'),
                        Forms\Components\DatePicker::make('meningitis_vaccine_date')
                            ->label('Tgl Vaksin Meningitis'),
                        Forms\Components\Toggle::make('has_covid_vaccine')
                            ->label('Vaksin COVID'),
                        Forms\Components\Textarea::make('medical_notes')
                            ->label('Catatan Medis')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('allergies')
                            ->label('Alergi')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('medications')
                            ->label('Obat-obatan')
                            ->columnSpanFull(),
                    ])->columns(2),
                Schemas\Components\Section::make('Lainnya')
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label('Cabang')
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('agent_id')
                            ->label('Agent')
                            ->options(User::where('role', 'agent')->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('source')
                            ->label('Sumber')
                            ->maxLength(50),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
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
                Tables\Columns\TextColumn::make('registration_number')
                    ->label('No. Registrasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Kota')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('passport_number')
                    ->label('Paspor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Gender')
                    ->options(PilgrimGender::class),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->relationship('branch', 'name'),
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ]),
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
            'index' => Pages\ListPilgrims::route('/'),
            'create' => Pages\CreatePilgrim::route('/create'),
            'edit' => Pages\EditPilgrim::route('/{record}/edit'),
        ];
    }
}
