<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
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
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-newspaper'; }
    public static function getNavigationGroup(): string|null { return 'Konten'; }
    public static function getNavigationSort(): int { return 2; }
    protected static ?string $navigationLabel = 'Artikel Blog';
    protected static ?string $label = 'Artikel';
    protected static ?string $pluralLabel = 'Artikel';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Konten Artikel')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Textarea::make('excerpt')
                            ->label('Ringkasan')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label('Konten')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('blog/content'),
                    ])->columns(2),
                Section::make('Kategori & Gambar')
                    ->schema([
                        Select::make('blog_category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->preload()
                            ->searchable(),
                        FileUpload::make('featured_image')
                            ->label('Gambar Utama')
                            ->image()
                            ->directory('blog/featured'),
                    ])->columns(2),
                Section::make('Publikasi')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Publikasikan')
                            ->default(false)
                            ->reactive(),
                        DateTimePicker::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->default(now())
                            ->visible(fn ($get) => $get('is_published')),
                        Select::make('user_id')
                            ->label('Penulis')
                            ->relationship('author', 'name')
                            ->required()
                            ->default(fn () => auth()->id())
                            ->preload()
                            ->searchable(),
                    ])->columns(3),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(70)
                            ->helperText('Maksimal 70 karakter. Jika kosong, akan menggunakan judul.'),
                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->maxLength(160)
                            ->helperText('Maksimal 160 karakter. Jika kosong, akan menggunakan ringkasan.'),
                    ])->columns(2)->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')->label('Gambar')->circular(),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->sortable()->limit(50),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori')->badge()->sortable(),
                Tables\Columns\TextColumn::make('author.name')->label('Penulis')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label('Publikasi')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->label('Tgl Terbit')->dateTime('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Diupdate')->dateTime('d M Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('blog_category_id')->label('Kategori')->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('is_published')->label('Status')->options(['1' => 'Dipublikasi', '0' => 'Draft']),
                Tables\Filters\SelectFilter::make('user_id')->label('Penulis')->relationship('author', 'name'),
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
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
