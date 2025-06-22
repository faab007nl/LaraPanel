<?php

namespace App\Providers\Filament;

use App\Filament\Installer\Pages\ApplicationConfigurationPage;
use App\Filament\Installer\Pages\DatabaseConfigurationPage;
use App\Filament\Installer\Pages\DatabaseInstallationPage;
use App\Filament\Installer\Pages\LicenseAgreementPage;
use App\Filament\Installer\Pages\SuccessPage;
use App\Filament\Installer\Pages\SystemRequirementsPage;
use App\Filament\Installer\Pages\WelcomePage;
use App\Http\Middleware\PanelInstalledRedirectMiddleware;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Vite;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class InstallerPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('installer')
            ->path('installer')
            ->colors([
                'primary' => Color::Green,
            ])
            ->pages([
                WelcomePage::class,
                SystemRequirementsPage::class,
                LicenseAgreementPage::class,
                DatabaseConfigurationPage::class,
                DatabaseInstallationPage::class,
                ApplicationConfigurationPage::class,
                SuccessPage::class
            ])
            ->navigationItems([
                NavigationItem::make('Custom Dashboard')
                    ->label('My Custom Dashboard')
                    ->icon('heroicon-o-home')
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard')),
            ])
            ->middleware([
                PanelInstalledRedirectMiddleware::class,
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
            ->brandLogo(fn () => view('filament.brand-logos.installer'))
            ->renderHook('panels::head.start',
                fn(): string => Vite::useHotFile('hot')
                    ->withEntryPoints(['resources/css/filament/install.css'])
                    ->toHtml());
    }

    public static function getUrl(): string
    {
        return config('app.url') . '/installer';
    }

}
