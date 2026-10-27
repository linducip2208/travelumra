<?php

namespace App\Filament\Resources;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Filament\Resources\BookingResource;
use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    public static function getNavigationIcon(): string|null { return 'heroicon-o-user-plus'; }

    public static function getNavigationGroup(): string|null { return 'CRM'; }
    public static function getNavigationSort(): int { return 1; }

    protected static ?string $navigationLabel = 'Prospek';

    protected static ?string $label = 'Prospek';

    protected static ?string $pluralLabel = 'Prospek';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'branch_manager', 'agent']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Data Prospek')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->required()
                            ->maxLength(30),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('city')
                            ->label('Kota')
                            ->maxLength(50),
                    ])->columns(2),
                Schemas\Components\Section::make('Detail Prospek')
                    ->schema([
                        Forms\Components\Select::make('interested_package_id')
                            ->label('Paket Diminati')
                            ->options(Package::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('source')
                            ->label('Sumber')
                            ->options(LeadSource::class)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(LeadStatus::class)
                            ->required()
                            ->default('new'),
                        Forms\Components\Select::make('assigned_to')
                            ->label('Ditugaskan ke')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('follow_up_date')
                            ->label('Tgl Follow Up'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Kota')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'contacted' => 'warning',
                        'interested' => 'success',
                        'follow_up' => 'primary',
                        'converted' => 'success',
                        'closed_lost' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Ditugaskan ke')
                    ->sortable(),
                Tables\Columns\TextColumn::make('follow_up_date')
                    ->label('Follow Up')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source')
                    ->label('Sumber')
                    ->options(LeadSource::class),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(LeadStatus::class),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Ditugaskan ke')
                    ->relationship('assignedTo', 'name'),
                Tables\Filters\Filter::make('follow_up_date')
                    ->label('Tgl Follow Up')
                    ->form([
                        Forms\Components\DatePicker::make('follow_up_from')
                            ->label('Dari'),
                        Forms\Components\DatePicker::make('follow_up_until')
                            ->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['follow_up_from'], fn ($q) => $q->whereDate('follow_up_date', '>=', $data['follow_up_from']))
                            ->when($data['follow_up_until'], fn ($q) => $q->whereDate('follow_up_date', '<=', $data['follow_up_until']));
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Actions\Action::make('convert_to_booking')
                    ->label('Jadikan Booking')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->visible(fn (Lead $record): bool => in_array($record->status, ['interested', 'follow_up']))
                    ->action(function (Lead $record) {
                        if (! $record->pilgrim_id) {
                            $pilgrim = Pilgrim::create([
                                'registration_number' => Pilgrim::generateRegistrationNumber(),
                                'full_name' => $record->name,
                                'phone' => $record->phone,
                                'email' => $record->email,
                                'city' => $record->city,
                                'source' => $record->source?->value ?? null,
                                'agent_id' => $record->assigned_to,
                                'is_active' => true,
                            ]);

                            $record->update([
                                'status' => 'converted',
                                'pilgrim_id' => $pilgrim->id,
                                'converted_at' => now(),
                            ]);

                            LeadActivity::create([
                                'lead_id' => $record->id,
                                'staff_id' => auth()->id(),
                                'type' => 'converted',
                                'description' => 'Prospek dikonversi menjadi jemaah #' . $pilgrim->registration_number,
                            ]);

                            return redirect(BookingResource::getUrl('create', ['pilgrim_id' => $pilgrim->id]));
                        }

                        return redirect(BookingResource::getUrl('create', ['pilgrim_id' => $record->pilgrim_id]));
                    }),
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
            RelationManagers\LeadActivityRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
