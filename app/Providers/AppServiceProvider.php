<?php

namespace App\Providers;

use App\Managers\EnvManager;
use App\Managers\Installer\SetupManager;
use App\Managers\SshManager;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->singleton('manager.setup', SetupManager::class);
        $this->app->singleton('manager.ssh', SshManager::class);
        $this->app->singleton('manager.env', EnvManager::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                ->modalHeading('Available Panels')
                ->simple()
                ->labels([
                    'admin' => 'Admin',
                    'sites' => 'sites'
                ])
                ->icons([
                    'admin' => 'heroicon-o-cog-6-tooth',
                    'sites' => 'heroicon-o-globe-alt'
                ])
                ->visible(function () {
                    return auth()->check() && auth()->user()->isAdmin();
                })
                ->panels(['admin', 'sites']);
        });

    }
}
