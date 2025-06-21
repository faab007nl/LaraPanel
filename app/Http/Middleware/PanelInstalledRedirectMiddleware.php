<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PanelInstalledRedirectMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {
        $setupManager = app('manager.setup');

        if ($setupManager->isInstalled()) {
            return redirect()->to("/");
        }

        return $next($request);
    }

}
