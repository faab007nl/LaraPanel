<?php

namespace App\Filament\Installer;

use Filament\Navigation\NavigationItem;
use Illuminate\Support\Str;

trait BaseInstallerStepPage
{

    public static function getRoutePath(): string
    {
        return Str::slug(static::$title);
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->icon(static::getNavigationIcon())
                ->sort(static::getNavigationSort())
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getNavigationItemActiveRoutePattern()))
                ->url("#"),
        ];
    }

}
