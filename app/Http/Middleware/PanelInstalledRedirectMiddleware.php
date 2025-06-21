<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PanelInstalledRedirectMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {
        $installedManager = app('installed.manager');

        if ($installedManager->isInstalled()) {
            return redirect()->to("/");
        }

        return $next($request);
    }

}
