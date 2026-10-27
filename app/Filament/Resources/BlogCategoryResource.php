<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogCategoryResource\Pages;
use App\Models\BlogCategory;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogCategoryResource extends Resource
{
    protected static ?string $model = BlogCategory::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-tag'; }
    public static function getNavigationGroup(): string|null { return 'Konten'; }
    public static function getNavigationSort(): int { return 1; }
    protected static ?string $navigationLabel = 'Kategori Blog';
    protected static ?string $label = 'Kategori Blog';
    protected static ?string $pluralLabel = 'Kategori Blog';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Kategori')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(100)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('posts_count')->label('Artikel')->counts('posts')->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Diupdate')->dateTime('d M Y H:i')->sortable(),
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
            'index' => Pages\ListBlogCategories::route('/'),
            'create' => Pages\CreateBlogCategory::route('/create'),
            'edit' => Pages\EditBlogCategory::route('/{record}/edit'),
        ];
    }
}
