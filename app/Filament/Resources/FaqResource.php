<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-question-mark-circle'; }
    public static function getNavigationGroup(): string|null { return 'Konten'; }
    public static function getNavigationSort(): int { return 4; }
    protected static ?string $navigationLabel = 'FAQ';
    protected static ?string $label = 'FAQ';
    protected static ?string $pluralLabel = 'FAQ';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Pertanyaan & Jawaban')
                    ->schema([
                        TextInput::make('question')
                            ->label('Pertanyaan')
                            ->required()
                            ->maxLength(500)
                            ->columnSpanFull(),
                        RichEditor::make('answer')
                            ->label('Jawaban')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('faq/content'),
                    ]),
                Section::make('Pengaturan')
                    ->schema([
                        TextInput::make('category')
                            ->label('Kategori')
                            ->maxLength(100)
                            ->datalist([
                                'Umum',
                                'Pendaftaran',
                                'Pembayaran',
                                'Keberangkatan',
                                'Hotel & Akomodasi',
                                'Visa & Dokumen',
                                'Perlengkapan',
                            ]),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_published')
                            ->label('Publikasikan')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')->label('Pertanyaan')->searchable()->limit(80),
                Tables\Columns\TextColumn::make('category')->label('Kategori')->badge()->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Diupdate')->dateTime('d M Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->label('Kategori')->options(fn () => Faq::distinct()->pluck('category', 'category')->filter()->toArray()),
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
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
