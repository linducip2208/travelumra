<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-document'; }
    public static function getNavigationGroup(): string|null { return 'Konten'; }
    public static function getNavigationSort(): int { return 3; }
    protected static ?string $navigationLabel = 'Halaman';
    protected static ?string $label = 'Halaman';
    protected static ?string $pluralLabel = 'Halaman';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Konten Halaman')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Halaman')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('URL halaman: domain.com/halaman/{slug}'),
                        Toggle::make('is_published')
                            ->label('Publikasikan')
                            ->default(false),
                        RichEditor::make('content')
                            ->label('Konten')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('pages/content'),
                    ])->columns(3),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(70)
                            ->helperText('Maksimal 70 karakter.'),
                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->maxLength(160)
                            ->helperText('Maksimal 160 karakter.'),
                    ])->columns(2)->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label('Publikasi')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Diupdate')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_published')->label('Status')->options(['1' => 'Dipublikasi', '0' => 'Draft']),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
