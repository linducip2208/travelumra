<?php
namespace App\Filament\Resources;
use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Tables; use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;
    public static function getNavigationIcon(): string|null { return 'heroicon-o-clock'; }
    public static function getNavigationGroup(): string|null { return 'Pengaturan'; }
    public static function getNavigationSort(): int { return 2; }
    protected static ?string $navigationLabel = 'Audit Log';
    protected static ?string $label = 'Audit Log'; protected static ?string $pluralLabel = 'Audit Log';

    public static function canAccess(): bool { return auth()->user()?->role === 'super_admin'; }

    public static function table(Table $table): Table { return $table->columns([
        Tables\Columns\TextColumn::make('user.name')->label('User')->searchable()->sortable(),
        Tables\Columns\TextColumn::make('action')->label('Aksi')->badge()->searchable(),
        Tables\Columns\TextColumn::make('entity_type')->label('Tipe')->searchable(),
        Tables\Columns\TextColumn::make('entity_id')->label('ID Entitas'),
        Tables\Columns\TextColumn::make('ip_address')->label('IP')->toggleable(),
        Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i:s')->sortable(),
    ])->defaultSort('created_at','desc')
    ->filters([
        Tables\Filters\SelectFilter::make('action')->options(['created'=>'Created','updated'=>'Updated','deleted'=>'Deleted']),
        Tables\Filters\SelectFilter::make('user_id')->relationship('user','name'),
    ])
    ->actions([])
    ->bulkActions([]); }

    public static function getRelations(): array { return []; }
    public static function getPages(): array { return ['index'=>Pages\ListAuditLogs::route('/')]; }
}
