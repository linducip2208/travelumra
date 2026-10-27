<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlightResource\Pages;
use App\Models\Flight;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class FlightResource extends Resource
{
    protected static ?string $model = Flight::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-paper-airplane'; }
    public static function getNavigationGroup(): string|null { return 'Operasional'; }
    public static function getNavigationSort(): int { return 7; }
    protected static ?string $navigationLabel = 'Penerbangan';
    protected static ?string $label = 'Penerbangan';
    protected static ?string $pluralLabel = 'Penerbangan';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Penerbangan')->schema([
                Select::make('cluster_id')->label('Kloter')->relationship('cluster', 'name')->preload()->searchable(),
                TextInput::make('airline')->label('Maskapai')->required()->maxLength(50),
                TextInput::make('flight_number')->label('No. Penerbangan')->required()->maxLength(20),
                TextInput::make('departure_airport')->label('Bandara Asal')->required()->maxLength(10),
                TextInput::make('arrival_airport')->label('Bandara Tujuan')->required()->maxLength(10),
                DateTimePicker::make('departure_time')->label('Waktu Berangkat')->required(),
                DateTimePicker::make('arrival_time')->label('Waktu Tiba')->required(),
                TextInput::make('terminal')->label('Terminal')->maxLength(10),
                TextInput::make('gate')->label('Gate')->maxLength(10),
                Select::make('status')->label('Status')->options([
                    'scheduled' => 'Terjadwal', 'boarding' => 'Boarding',
                    'departed' => 'Berangkat', 'arrived' => 'Tiba', 'delayed' => 'Delay', 'cancelled' => 'Dibatalkan',
                ])->default('scheduled')->required(),
                Textarea::make('notes')->label('Catatan')->columnSpanFull(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('flight_number')->label('No. Penerbangan')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('airline')->label('Maskapai')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('cluster.code')->label('Kloter')->sortable(),
            Tables\Columns\TextColumn::make('departure_airport')->label('Dari')->sortable(),
            Tables\Columns\TextColumn::make('arrival_airport')->label('Ke')->sortable(),
            Tables\Columns\TextColumn::make('departure_time')->label('Berangkat')->dateTime('d M Y H:i')->sortable(),
            Tables\Columns\TextColumn::make('duration')->label('Durasi'),
            Tables\Columns\TextColumn::make('status')->label('Status')->badge()->color(fn ($s) => match($s) {
                'scheduled' => 'gray', 'boarding' => 'warning', 'departed' => 'info',
                'arrived' => 'success', 'delayed' => 'danger', 'cancelled' => 'danger',
                default => 'gray',
            }),
        ])->defaultSort('departure_time', 'desc')
        ->actions([Actions\EditAction::make(), Actions\DeleteAction::make()])
        ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlights::route('/'),
            'create' => Pages\CreateFlight::route('/create'),
            'edit' => Pages\EditFlight::route('/{record}/edit'),
        ];
    }
}
