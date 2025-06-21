<?php

namespace App\Http\Middleware;

use App\Providers\Filament\InstallerPanelProvider;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PanelRequiresInstallRedirectMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {
        $installedManager = app('installed.manager');

        if (!$installedManager->isInstalled()) {
            return redirect()->to(InstallerPanelProvider::getUrl());
        }

        return $next($request);
    }

}
