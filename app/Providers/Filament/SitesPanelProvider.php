<?php

namespace App\Providers\Filament;

use App\Http\Middleware\PanelRequiresInstallRedirectMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Vite;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SitesPanelProvider extends PanelProvider
{


    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('sites')
            ->path('')
            ->login()
            ->colors([
                'primary' => Color::Cyan,
            ])
            ->topNavigation() // Hides side nav
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->items([]);
            }) // Removes all nav items
            ->discoverResources(in: app_path('Filament/Sites/Resources'), for: 'App\\Filament\\Sites\\Resources')
            ->discoverPages(in: app_path('Filament/Sites/Pages'), for: 'App\\Filament\\Sites\\Pages')
            ->pages([
                //
            ])
            ->middleware([
                PanelRequiresInstallRedirectMiddleware::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->brandLogo(fn () => view('filament.brand-logos.sites'))
            ->renderHook('panels::head.start',
                fn(): string => Vite::useHotFile('hot')
                    ->withEntryPoints(['resources/css/filament/sites.css'])
                    ->toHtml());
    }

    public static function getUrl(): string
    {
        return config('app.url') . '/sites';
    }

}
