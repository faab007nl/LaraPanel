<?php

namespace App\Filament\Installer\Pages;

use App\Filament\Installer\BaseInstallerStepPage;
use Filament\Pages\Page;

class WelcomePage extends Page
{
    use BaseInstallerStepPage;

    protected static string $view = 'filament.installer.pages.welcome-page';

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';
    protected static ?string $title = 'Welcome';

}
