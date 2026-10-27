<?php

namespace App\Filament\Pages;

use App\Models\LoyaltyPoint;
use App\Models\Pilgrim;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class LoyaltyRedemption extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.loyalty-redemption';

    protected static string | \UnitEnum | null $navigationGroup = 'CRM';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-gift';
    protected static ?int $navigationSort = 31;
    protected static ?string $title = 'Tukar Poin Loyalitas';
    protected static ?string $navigationLabel = 'Tukar Poin';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin', 'agent']);
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('pilgrim_id')
                    ->label('Jamaah')
                    ->options(fn () => Pilgrim::where('is_active', true)->pluck('full_name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $total = LoyaltyPoint::where('pilgrim_id', $state)
                                ->where('transaction_type', 'earned')
                                ->sum('points');
                            $used = LoyaltyPoint::where('pilgrim_id', $state)
                                ->where('transaction_type', 'redeemed')
                                ->sum('points');
                            $set('available_points', $total - $used);
                        }
                    }),
                TextInput::make('available_points')
                    ->label('Poin Tersedia')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('points')
                    ->label('Poin Ditukar')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                TextInput::make('value_rupiah')
                    ->label('Nilai (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('1 poin = Rp 1.000')
                    ->required(),
                TextInput::make('description')
                    ->label('Keterangan')
                    ->maxLength(255)
                    ->required(),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function redeem(): void
    {
        $state = $this->form->getState();

        $total = LoyaltyPoint::where('pilgrim_id', $state['pilgrim_id'])
            ->where('transaction_type', 'earned')
            ->sum('points');
        $used = LoyaltyPoint::where('pilgrim_id', $state['pilgrim_id'])
            ->where('transaction_type', 'redeemed')
            ->sum('points');
        $available = $total - $used;

        if ($state['points'] > $available) {
            Notification::make()->danger()->title('Poin tidak mencukupi')->send();
            return;
        }

        LoyaltyPoint::create([
            'pilgrim_id' => $state['pilgrim_id'],
            'booking_id' => null,
            'points' => $state['points'],
            'transaction_type' => 'redeemed',
            'description' => $state['description'],
            'value_rupiah' => $state['value_rupiah'],
        ]);

        Notification::make()->success()->title('Poin berhasil ditukar')->send();
        $this->form->fill();
    }
}
