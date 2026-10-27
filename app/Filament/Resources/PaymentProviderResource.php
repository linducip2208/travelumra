<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentProviderResource\Pages;
use App\Models\PaymentProvider;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Support\Facades\Storage;

class PaymentProviderResource extends Resource
{
    protected static ?string $model = PaymentProvider::class;
    protected static ?string $navigationLabel = 'Payment Gateway';
    protected static ?string $label = 'Payment Gateway';
    protected static ?string $pluralLabel = 'Payment Gateway';

    public static function getNavigationIcon(): string|null { return 'heroicon-o-credit-card'; }
    public static function getNavigationGroup(): string|null { return 'Integrasi'; }
    public static function getNavigationSort(): int { return 1; }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin']);
    }

    protected static function getPresets(): array
    {
        $path = storage_path('app/payment-presets/indonesia.json');
        if (!file_exists($path)) return [];
        return collect(json_decode(file_get_contents($path), true))
            ->mapWithKeys(fn ($p) => [$p['code'] => $p['name'] . ' — ' . $p['features']])
            ->toArray();
    }

    public static function form(Schema $schema): Schema
    {
        $presets = json_decode(file_get_contents(storage_path('app/payment-presets/indonesia.json')), true) ?? [];

        return $schema
            ->schema([
                Section::make('Pilih Gateway (Auto-Fill)')
                    ->description('Pilih payment gateway dari daftar untuk mengisi otomatis. Atau isi manual.')
                    ->collapsible()
                    ->schema([
                        Select::make('_preset')
                            ->label('Gateway Preset')
                            ->options(collect($presets)->mapWithKeys(fn ($p) => [$p['code'] => $p['name']])->toArray())
                            ->placeholder('— Pilih gateway untuk auto-fill, atau kosongkan untuk isi manual —')
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) use ($presets) {
                                if (!$state) return;
                                $preset = collect($presets)->firstWhere('code', $state);
                                if (!$preset) return;
                                if (empty($get('name'))) $set('name', $preset['name']);
                                if (empty($get('code'))) $set('code', $preset['code']);
                                if (empty($get('api_format')) || $get('api_format') === 'simulator') $set('api_format', $preset['api_format']);
                                if (empty($get('base_url'))) $set('base_url', $preset['sandbox_url']);
                            })
                            ->columnSpanFull(),
                        Placeholder::make('_preset_info')
                            ->content(function (Get $get) use ($presets) {
                                $code = $get('_preset');
                                if (!$code) return 'Pilih gateway di atas untuk info selengkapnya.';
                                $preset = collect($presets)->firstWhere('code', $code);
                                if (!$preset) return '';
                                return "{$preset['name']} — {$preset['features']}\nDocs: {$preset['docs_url']}\nSandbox: {$preset['sandbox_url']}\nLive: {$preset['base_url']}";
                            })
                            ->columnSpanFull(),
                    ]),
                Section::make('Provider')
                    ->schema([
                        TextInput::make('name')->label('Nama')->required()->maxLength(100),
                        TextInput::make('code')->label('Kode')->required()->maxLength(50)->unique(ignoreRecord: true),
                        Select::make('api_format')->label('Format API')->options([
                            'simulator' => 'Simulator (Tanpa API — Testing)',
                            'redirect_snap' => 'Redirect Snap (Midtrans)',
                            'redirect' => 'Redirect (Xendit, Doku, OY!, dll)',
                            'redirect_tripay' => 'Redirect Tripay',
                            'direct_api' => 'Direct API (iPaymu, Nicepay)',
                            'ewallet' => 'eWallet Direct (LinkAja, Doku Wallet)',
                            'qr' => 'QRIS (QR Code)',
                        ])->required()->default('simulator')->reactive(),
                        TextInput::make('base_url')->label('Base URL')->maxLength(255)
                            ->helperText(fn ($get) => match($get('api_format')) {
                                'redirect_tripay' => 'Default: https://tripay.co.id/api-sandbox (sandbox) / https://tripay.co.id/api (live)',
                                'redirect_snap' => 'Default: https://app.sandbox.midtrans.com/snap/v1 (sandbox) / https://app.midtrans.com/snap/v1 (live)',
                                'direct_api' => 'Isi endpoint API gateway Anda',
                                'ewallet' => 'Isi base URL eWallet API',
                                'qr' => 'Isi base URL QRIS provider',
                                default => 'Isi base URL gateway Anda',
                            }),
                        Toggle::make('is_active')->label('Aktif')->default(true),
                        Toggle::make('is_default')->label('Default')->default(false),
                        TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
                    ])->columns(2),
                Section::make('Kredensial (terenkripsi)')
                    ->visible(fn ($get) => $get('api_format') !== 'simulator')
                    ->schema([
                        TextInput::make('server_key_encrypted')->label('Server Key / API Key')
                            ->password()->revealable()
                            ->dehydrateStateUsing(fn ($state) => $state ? encrypt($state) : null)
                            ->afterStateHydrated(fn ($component, $state) => $component->state($state ? rescue(fn () => decrypt($state), '') : ''))
                            ->helperText(fn ($get) => match($get('api_format')) {
                                'redirect_tripay' => 'Tripay API Key dari dashboard Tripay',
                                'redirect_snap' => 'Midtrans Server Key: Settings → Access Keys',
                                'direct_api' => 'API Key / Secret Key gateway Anda',
                                'ewallet' => 'API Key eWallet provider',
                                'qr' => 'API Key QRIS provider',
                                default => 'API Key / Server Key gateway Anda',
                            }),
                        TextInput::make('client_key_encrypted')->label('Client Key')
                            ->password()->revealable()
                            ->dehydrateStateUsing(fn ($state) => $state ? encrypt($state) : null)
                            ->afterStateHydrated(fn ($component, $state) => $component->state($state ? rescue(fn () => decrypt($state), '') : ''))
                            ->helperText('Midtrans: Client Key dari Access Keys. Gateway lain: opsional.'),
                        TextInput::make('merchant_id')->label('Merchant ID / Merchant Code')->maxLength(50)
                            ->helperText('Tripay: Merchant Code. Midtrans: Merchant ID. Gateway lain: sesuai format.'),
                        KeyValue::make('extra_config')->label('Konfigurasi Tambahan')
                            ->keyLabel('Key')->valueLabel('Value')
                            ->helperText('Tripay: tambahkan private_key. iPaymu: tambahkan va. Midtrans: kosongkan.')
                            ->visible(fn ($get) => in_array($get('api_format'), ['redirect_tripay', 'direct_api', 'ewallet', 'qr'])),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Gateway')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('code')->label('Kode')->searchable()->fontFamily('mono'),
                Tables\Columns\TextColumn::make('api_format')->label('Format API')->badge()->color(fn ($state) => match($state) {
                    'redirect_snap' => 'success',
                    'redirect' => 'info',
                    'redirect_tripay' => 'primary',
                    'direct_api' => 'warning',
                    'ewallet' => 'violet',
                    'qr' => 'cyan',
                    'simulator' => 'gray',
                    default => 'gray',
                }),
                Tables\Columns\TextColumn::make('base_url')->label('URL')->limit(30)->fontFamily('mono')->size('xs')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\IconColumn::make('is_default')->label('Default')->boolean(),
            ])
            ->defaultSort('sort_order')
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentProviders::route('/'),
            'create' => Pages\CreatePaymentProvider::route('/create'),
            'edit' => Pages\EditPaymentProvider::route('/{record}/edit'),
        ];
    }
}
