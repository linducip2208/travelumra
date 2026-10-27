<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-chat-bubble-left-right'; }
    public static function getNavigationGroup(): string|null { return 'Konten'; }
    public static function getNavigationSort(): int { return 5; }
    protected static ?string $navigationLabel = 'Testimoni';
    protected static ?string $label = 'Testimoni';
    protected static ?string $pluralLabel = 'Testimoni';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Testimoni')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Jamaah')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('role')
                            ->label('Paket / Peran')
                            ->maxLength(100)
                            ->helperText('Contoh: Umrah Reguler, Haji Plus, Keluarga'),
                        Select::make('rating')
                            ->label('Rating')
                            ->options([
                                5 => '⭐⭐⭐⭐⭐ (5)',
                                4 => '⭐⭐⭐⭐ (4)',
                                3 => '⭐⭐⭐ (3)',
                                2 => '⭐⭐ (2)',
                                1 => '⭐ (1)',
                            ])
                            ->required()
                            ->default(5),
                        FileUpload::make('photo')
                            ->label('Foto Jamaah')
                            ->image()
                            ->directory('testimonials')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1'),
                        Textarea::make('content')
                            ->label('Isi Testimoni')
                            ->required()
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make('Pengaturan')
                    ->schema([
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_published')
                            ->label('Publikasikan')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')->label('Foto')->circular(),
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('role')->label('Paket')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('rating')->label('Rating')->sortable()->formatStateUsing(fn ($state) => str_repeat('⭐', (int) $state)),
                Tables\Columns\TextColumn::make('content')->label('Testimoni')->limit(80)->searchable(),
                Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label('Aktif')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')->label('Rating')->options([5 => '5', 4 => '4', 3 => '3', 2 => '2', 1 => '1']),
                Tables\Filters\SelectFilter::make('is_published')->label('Status')->options(['1' => 'Aktif', '0' => 'Draft']),
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
