<?php
namespace App\Filament\Resources;
use App\Filament\Resources\CheckInResource\Pages;
use App\Models\CheckIn;
use Filament\Forms\Components\{DateTimePicker, Select, Textarea};
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables; use Filament\Actions; use Filament\Tables\Table;

class CheckInResource extends Resource
{
    protected static ?string $model = CheckIn::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-check-circle'; }
    public static function getNavigationGroup(): string|null { return 'Operasional'; }
    public static function getNavigationSort(): ?int { return 11; }
    protected static ?string $navigationLabel = 'Check-in Jamaah';
    protected static ?string $label = 'Check-in'; protected static ?string $pluralLabel = 'Check-in';

    public static function canAccess(): bool { return in_array(auth()->user()?->role, ['super_admin','admin','muthawif']); }

    public static function form(Schema $schema): Schema { return $schema->schema([
        Section::make('Check-in/out')->schema([
            Select::make('booking_id')->label('Booking')->relationship('booking','booking_number')->searchable()->preload()->required(),
            Select::make('cluster_id')->label('Kloter')->relationship('cluster','name')->searchable()->preload(),
            DateTimePicker::make('checked_in_at')->label('Check-in'),
            DateTimePicker::make('checked_out_at')->label('Check-out'),
            Textarea::make('notes')->label('Catatan')->columnSpanFull(),
        ])->columns(2),
    ]); }

    public static function table(Table $table): Table { return $table->columns([
        Tables\Columns\TextColumn::make('booking.booking_number')->label('No. Booking')->searchable()->sortable(),
        Tables\Columns\TextColumn::make('booking.pilgrim.full_name')->label('Jamaah')->searchable(),
        Tables\Columns\TextColumn::make('cluster.code')->label('Kloter')->sortable(),
        Tables\Columns\TextColumn::make('checked_in_at')->label('Check-in')->dateTime('d M Y H:i')->sortable(),
        Tables\Columns\TextColumn::make('checked_out_at')->label('Check-out')->dateTime('d M Y H:i'),
        Tables\Columns\TextColumn::make('checkedBy.name')->label('Oleh'),
    ])->defaultSort('created_at','desc')
    ->actions([Actions\EditAction::make(),Actions\DeleteAction::make()])
    ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]); }

    public static function getRelations(): array { return []; }
    public static function getPages(): array { return [
        'index'=>Pages\ListCheckIns::route('/'),
        'create'=>Pages\CreateCheckIn::route('/create'),
        'edit'=>Pages\EditCheckIn::route('/{record}/edit'),
    ]; }
}
