<?php

namespace App\Filament\Installer;

use Filament\Actions\Action;
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
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getNavigationItemActiveRoutePattern()))
                ->sort(static::getNavigationSort())
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->badgeTooltip(static::getNavigationBadgeTooltip())
                ->url(static::getNavigationUrl()),
        ];
    }

    private function getNextStepAction(string $nextStepUrl, string $title)
    {
        return Action::make($title)
            ->url($nextStepUrl)
            ->icon('heroicon-o-arrow-right')
            ->color('primary');
    }

}
