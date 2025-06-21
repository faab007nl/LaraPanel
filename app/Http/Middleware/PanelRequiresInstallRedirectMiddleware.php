<?php

namespace App\Http\Middleware;

use App\Filament\Installer\Pages\WelcomePage;
use App\Providers\Filament\InstallerPanelProvider;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PanelRequiresInstallRedirectMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {
        $setupManager = app('manager.setup');

        if (!$setupManager->isInstalled()) {
            return redirect()->to(InstallerPanelProvider::getUrl() . '/' . WelcomePage::getRoutePath());
        }

        return $next($request);
    }

}
