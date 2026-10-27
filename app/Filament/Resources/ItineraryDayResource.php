<?php
namespace App\Filament\Resources;
use App\Filament\Resources\ItineraryDayResource\Pages;
use App\Filament\Resources\ItineraryDayResource\RelationManagers;
use App\Models\ItineraryDay;
use Filament\Forms\Components\{DatePicker, TextInput, Textarea, RichEditor};
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables; use Filament\Actions; use Filament\Tables\Table;

class ItineraryDayResource extends Resource
{
    protected static ?string $model = ItineraryDay::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-calendar-days'; }
    public static function getNavigationGroup(): string|null { return 'Operasional'; }
    public static function getNavigationSort(): ?int { return 10; }
    protected static ?string $navigationLabel = 'Itinerary Harian';
    protected static ?string $label = 'Itinerary'; protected static ?string $pluralLabel = 'Itinerary';

    public static function canAccess(): bool { return in_array(auth()->user()?->role, ['super_admin','admin']); }

    public static function form(Schema $schema): Schema { return $schema->schema([
        Section::make('Itinerary')->schema([
            \Filament\Forms\Components\Select::make('cluster_id')->label('Kloter')->relationship('cluster','name')->searchable()->preload()->required(),
            TextInput::make('day_number')->label('Hari Ke')->numeric()->required(),
            DatePicker::make('date')->label('Tanggal')->required(),
            TextInput::make('title')->label('Judul')->maxLength(255),
            TextInput::make('location')->label('Lokasi')->maxLength(100),
            Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
        ])->columns(3),
    ]); }

    public static function table(Table $table): Table { return $table->columns([
        Tables\Columns\TextColumn::make('cluster.code')->label('Kloter')->searchable()->sortable(),
        Tables\Columns\TextColumn::make('day_number')->label('Hari')->sortable(),
        Tables\Columns\TextColumn::make('date')->label('Tanggal')->date('d M Y')->sortable(),
        Tables\Columns\TextColumn::make('title')->label('Judul')->searchable(),
        Tables\Columns\TextColumn::make('location')->label('Lokasi'),
        Tables\Columns\TextColumn::make('activities_count')->label('Aktivitas')->counts('activities'),
    ])->defaultSort('cluster_id')
    ->actions([Actions\EditAction::make(),Actions\DeleteAction::make()])
    ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]); }

    public static function getRelations(): array { return [RelationManagers\ActivitiesRelationManager::class]; }
    public static function getPages(): array { return [
        'index'=>Pages\ListItineraryDays::route('/'),
        'create'=>Pages\CreateItineraryDay::route('/create'),
        'edit'=>Pages\EditItineraryDay::route('/{record}/edit'),
    ]; }
}
