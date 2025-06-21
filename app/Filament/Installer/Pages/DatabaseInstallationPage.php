<?php

namespace App\Filament\Installer\Pages;

use App\Filament\Installer\BaseInstallerStepPage;
use Filament\Pages\Page;

class DatabaseInstallationPage extends Page
{
    use BaseInstallerStepPage;

    protected static string $view = 'filament.installer.pages.database-installation-page';


    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $title = 'Database Installation';

    protected function getHeaderActions(): array
    {
        return [
            $this->getNextStepAction(PostInstallPage::getRoutePath(), 'Next')
        ];
    }

}
