<?php
namespace App\Filament\Resources\ClusterResource\RelationManagers;
use App\Models\Gallery;
use Filament\Forms\Components\{FileUpload, TextInput};
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables; use Filament\Actions; use Filament\Tables\Table;

class GalleryRelationManager extends RelationManager
{
    protected static string $relationship = 'gallery';
    protected static ?string $title = 'Galeri Foto';

    public function form(Schema $schema): Schema { return $schema->schema([
        FileUpload::make('file_path')->label('Foto')->image()->directory('gallery/clusters')->required(),
        TextInput::make('caption')->label('Caption')->maxLength(255),
        TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
    ]); }

    public function table(Table $table): Table { return $table->columns([
        Tables\Columns\ImageColumn::make('file_path')->label('Foto'),
        Tables\Columns\TextColumn::make('caption')->label('Caption')->searchable(),
        Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
    ])->defaultSort('sort_order')
    ->actions([Actions\EditAction::make(),Actions\DeleteAction::make()]); }
}
