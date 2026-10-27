@php
    use Filament\Support\Enums\Width;

    $livewire ??= null;

    $isLogin = request()->is('admin/login')
        || request()->is('*/login')
        || request()->routeIs('filament.admin.auth.login');

    $renderHookScopes = $livewire?->getRenderHookScopes();
    $maxContentWidth ??= (filament()->getSimplePageMaxContentWidth() ?? Width::Large);

    if (is_string($maxContentWidth)) {
        $maxContentWidth = Width::tryFrom($maxContentWidth) ?? $maxContentWidth;
    }
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    @props([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ])

    @if ($isLogin)
        <div style="display:flex;min-height:100vh;align-items:center;justify-content:center;background:linear-gradient(135deg,#f0f4ff,#e8ecf8,#f8fafc,#eef2ff);padding:2rem;">
            <div style="width:100%;max-width:400px;background:#fff;border-radius:20px;padding:2.5rem 2rem;box-shadow:0 1px 3px rgba(0,0,0,.04),0 4px 16px rgba(0,0,0,.06),0 16px 48px rgba(79,70,229,.08);">
                <style>
                    .fi-login-card-input { border:1.5px solid #d1d5db !important; background:#fff !important; color:#111827 !important; border-radius:10px !important; padding:11px 14px !important; font-size:14px !important; box-shadow:0 1px 2px rgba(0,0,0,.04) !important; }
                    .fi-login-card-input:focus { border-color:#6366f1 !important; box-shadow:0 0 0 3px rgba(99,102,241,.15) !important; }
                    .fi-login-card .fi-input { border:1.5px solid #d1d5db !important; background:#fff !important; color:#111827 !important; border-radius:10px !important; padding:11px 14px !important; font-size:14px !important; box-shadow:0 1px 2px rgba(0,0,0,.04) !important; }
                    .fi-login-card .fi-input:focus { border-color:#6366f1 !important; box-shadow:0 0 0 3px rgba(99,102,241,.15) !important; }
                    .fi-login-card button[type="submit"], .fi-login-card .fi-btn { display:inline-flex !important; align-items:center !important; justify-content:center !important; width:100% !important; padding:13px 24px !important; background:linear-gradient(135deg,#6366f1,#4f46e5) !important; color:#fff !important; font-size:14px !important; font-weight:600 !important; border-radius:10px !important; border:none !important; cursor:pointer !important; box-shadow:0 2px 8px rgba(79,70,229,.3) !important; margin-top:8px !important; }
                </style>
                <div class="fi-login-card">
                    {{ $slot }}
                </div>
            </div>
        </div>
    @else
        <div class="fi-simple-layout">
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_START, scopes: $renderHookScopes) }}

            @if (($hasTopbar ?? true) && filament()->auth()->check())
                <div class="fi-simple-layout-header">
                    @if (filament()->hasDatabaseNotifications())
                        @livewire(filament()->getDatabaseNotificationsLivewireComponent(), [
                            'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
                            'position' => \Filament\Enums\DatabaseNotificationsPosition::Topbar,
                        ])
                    @endif

                    @if (filament()->hasUserMenu())
                        @livewire(Filament\Livewire\SimpleUserMenu::class)
                    @endif
                </div>
            @endif

            <div class="fi-simple-main-ctn">
                <main @class([
                    'fi-simple-main',
                    ($maxContentWidth instanceof Width) ? "fi-width-{$maxContentWidth->value}" : $maxContentWidth,
                ])>
                    {{ $slot }}
                </main>
            </div>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_END, scopes: $renderHookScopes) }}
        </div>
    @endif
</x-filament-panels::layout.base>
