<?php

namespace App\Filament\Resources;

use App\Enums\ClusterStatus;
use App\Filament\Resources\ClusterResource\Pages;
use App\Filament\Resources\ClusterResource\RelationManagers;
use App\Models\Cluster;
use App\Models\Package;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ClusterResource extends Resource
{
    protected static ?string $model = Cluster::class;

    public static function getNavigationIcon(): string|null { return 'heroicon-o-globe-alt'; }

    public static function getNavigationGroup(): string|null { return 'Operasional'; }
    public static function getNavigationSort(): int { return 4; }

    protected static ?string $navigationLabel = 'Kloter';

    protected static ?string $label = 'Kloter';

    protected static ?string $pluralLabel = 'Kloter';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'branch_manager']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Informasi Kloter')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Kloter')
                            ->required()
                            ->maxLength(20)
                            ->default(fn () => 'KLT-' . Str::upper(Str::random(6)))
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kloter')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Select::make('package_id')
                            ->label('Paket')
                            ->relationship('package', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('muthawif_id')
                            ->label('Muthawif')
                            ->options(User::where('is_muthawif', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('capacity')
                            ->label('Kapasitas')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        Forms\Components\TextInput::make('booked_count')
                            ->label('Terisi')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(ClusterStatus::class)
                            ->required()
                            ->default('scheduled'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kloter')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('package.name')
                    ->label('Paket')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('muthawif.name')
                    ->label('Muthawif')
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('booked_count')
                    ->label('Terisi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'warning',
                        'departed' => 'info',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(ClusterStatus::class),
                Tables\Filters\SelectFilter::make('package_id')
                    ->label('Paket')
                    ->relationship('package', 'name'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ClusterPilgrimRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClusters::route('/'),
            'create' => Pages\CreateCluster::route('/create'),
            'edit' => Pages\EditCluster::route('/{record}/edit'),
        ];
    }
}
