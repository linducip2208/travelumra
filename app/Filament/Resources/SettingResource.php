<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    public static function getNavigationIcon(): string|null { return 'heroicon-o-cog'; }

    public static function getNavigationGroup(): string|null { return 'Pengaturan'; }
    public static function getNavigationSort(): int { return 1; }

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $label = 'Pengaturan';

    protected static ?string $pluralLabel = 'Pengaturan';

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'super_admin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Dasar')
                    ->schema([
                        TextInput::make('key')
                            ->label('Key')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText('Gunakan snake_case tanpa spasi. Contoh: hero_title, contact_phone'),
                        Select::make('group')
                            ->label('Grup')
                            ->options([
                                'marketing' => 'Marketing',
                                'contact' => 'Kontak',
                                'general' => 'Umum',
                                'system' => 'Sistem',
                            ])
                            ->required()
                            ->default('general')
                            ->reactive()
                            ->afterStateUpdated(fn ($state, $set) => $set('key', '')),
                        TextInput::make('label')
                            ->label('Label')
                            ->maxLength(100)
                            ->helperText('Nama tampilan pengaturan ini (hanya untuk admin)'),
                    ])->columns(3),

                Section::make('Nilai')
                    ->schema([
                        RichEditor::make('value')
                            ->label(fn ($get) => match ($get('group')) {
                                'marketing' => 'Konten Marketing',
                                'contact' => 'Informasi Kontak',
                                default => 'Value',
                            })
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('settings/content')
                            ->visible(fn ($get) => $get('group') === 'marketing'),
                        Textarea::make('value')
                            ->label(fn ($get) => match ($get('group')) {
                                'contact' => 'Informasi Kontak',
                                default => 'Value',
                            })
                            ->required()
                            ->columnSpanFull()
                            ->helperText(fn ($get) => match ($get('group')) {
                                'contact' => 'Nomor telepon, WhatsApp, alamat, dll.',
                                default => 'Nilai pengaturan',
                            })
                            ->visible(fn ($get) => $get('group') !== 'marketing'),
                        Select::make('type')
                            ->label('Tipe Data')
                            ->options([
                                'string' => 'String (Teks)',
                                'json' => 'JSON',
                                'boolean' => 'Boolean (true/false)',
                                'integer' => 'Integer (Angka)',
                            ])
                            ->required()
                            ->default('string'),
                    ]),

                Section::make('Keterangan')
                    ->schema([
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->helperText('Jelaskan fungsi pengaturan ini untuk referensi admin'),
                    ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('group')
                    ->label('Grup')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Grup')
                    ->options(fn () => Setting::distinct()->pluck('group', 'group')->filter()->toArray()),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'string' => 'String',
                        'json' => 'JSON',
                        'boolean' => 'Boolean',
                        'integer' => 'Integer',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
