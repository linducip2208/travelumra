<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Admin')
            ->brandLogo(fn () => new \Illuminate\Support\HtmlString('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="32" height="32" fill="none"><rect width="48" height="48" rx="12" fill="url(#a)"/><path d="M24 12c-2.5 0-4.5 1-6 2.5l-1.5-1.5C18.5 11 21 10 24 10s5.5 1 7.5 3L30 14.5C28.5 13 26.5 12 24 12zm8 6l-2 2c1.5 1.5 2.5 3.5 2.5 6H36c0-3.5-1.5-6.5-4-8zm-16 0c-2.5 1.5-4 4.5-4 8h3.5c0-2.5 1-4.5 2.5-6l-2-2zm3 5a5 5 0 1 0 10 0 5 5 0 0 0-10 0zm-5 7a3 3 0 1 0 0 6h20a3 3 0 1 0 0-6H14z" fill="#fff"/><defs><linearGradient id="a" x1="0" y1="0" x2="48" y2="48"><stop stop-color="#6366f1"/><stop offset="1" stop-color="#7c3aed"/></linearGradient></defs></svg>'))
            ->favicon(fn () => new \Illuminate\Support\HtmlString('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32" fill="none"><rect width="32" height="32" rx="8" fill="#6366f1"/><path d="M16 8c-1.5 0-3 .7-4 1.8l-1-1C12.5 7.2 14 6.5 16 6.5s3.5.7 5 2.3L20 9.8C19 8.7 17.5 8 16 8zm5 4l-1.3 1.3c1 1 1.7 2.3 1.7 4H24c0-2.3-1-4.3-3-5.3zm-10 0c-2 1-3 3-3 5.3h2.7c0-1.7.7-3 1.6-4L11 12zm2 3.5a3.5 3.5 0 1 0 7 0 3.5 3.5 0 0 0-7 0zm-3.3 4.5a2 2 0 1 0 0 4h13.3a2 2 0 1 0 0-4H9.7z" fill="#fff"/></svg>'))
            ->colors(['primary' => Color::Indigo])
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop(true)
            ->sidebarWidth('15.5rem')
            ->collapsedSidebarWidth('4rem')
            ->topbar(true)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->databaseNotifications()
            ->navigationGroups([
                'Master Data',
                'Penjualan',
                'Keuangan',
                'Operasional',
                'CRM',
                'Dokumen',
                'Laporan',
                'Konten',
                'Integrasi',
                'Pengaturan',
            ])
            ->plugins([])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
