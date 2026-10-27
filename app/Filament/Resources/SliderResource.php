<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Models\Slider;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-photo'; }
    public static function getNavigationGroup(): string|null { return 'Konten'; }
    public static function getNavigationSort(): int { return 6; }
    protected static ?string $navigationLabel = 'Slider';
    protected static ?string $label = 'Slider';
    protected static ?string $pluralLabel = 'Slider';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Konten Slide')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('subtitle')
                            ->label('Sub Judul')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('Gambar')
                            ->image()
                            ->directory('sliders')
                            ->imageResizeMode('cover')
                            ->helperText('Rekomendasi: 1920x900px'),
                    ])->columns(2),
                Section::make('Link & Pengaturan')
                    ->schema([
                        TextInput::make('link_url')
                            ->label('Link URL')
                            ->maxLength(255)
                            ->helperText('Contoh: /paket-umrah atau https://...'),
                        TextInput::make('link_text')
                            ->label('Teks Link')
                            ->maxLength(50)
                            ->helperText('Contoh: Lihat Paket, Daftar Sekarang'),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Gambar'),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->sortable()->limit(50),
                Tables\Columns\TextColumn::make('subtitle')->label('Sub Judul')->limit(60)->toggleable(),
                Tables\Columns\TextColumn::make('link_url')->label('Link')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Diupdate')->dateTime('d M Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')->label('Status')->options(['1' => 'Aktif', '0' => 'Nonaktif']),
            ])
            ->defaultSort('sort_order', 'asc')
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
