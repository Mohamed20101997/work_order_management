<?php

namespace App\Providers\Filament;

use App\Http\Middleware\LocalizationMiddleware;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
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
            ->brandName('Asset Management')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->login()
            ->passwordReset()
            ->profile(\App\Filament\Pages\Auth\EditProfile::class, isSimple: false)
            ->databaseTransactions()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                LocalizationMiddleware::class,
                ShareErrorsFromSession::class,
                ValidateCsrfToken::class,
                SubstituteBindings::class,
            ])
            ->authMiddleware([
                \Filament\Http\Middleware\Authenticate::class,
            ])
            ->userMenuItems([
                'language-en' => MenuItem::make()
                    ->label('English')
                    ->icon('heroicon-o-globe-americas')
                    ->url(fn (): string => route('locale.switch', 'en')),
                'language-ar' => MenuItem::make()
                    ->label('العربية')
                    ->icon('heroicon-o-globe-americas')
                    ->url(fn (): string => route('locale.switch', 'ar')),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                'panels::head.start',
                fn () => view('filament.hooks.pwa-head'),
            )
            ->renderHook(
                'panels::body.end',
                fn () => view('filament.hooks.pwa-install'),
            );
    }
}
