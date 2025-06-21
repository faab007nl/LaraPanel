<?php

namespace App\Filament\Installer\Pages;

use App\Filament\Installer\BaseInstallerStepPage;
use Filament\Pages\Page;

class ApplicationConfigurationPage extends Page
{
    use BaseInstallerStepPage;

    protected static string $view = 'filament.installer.pages.form-page';


    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $title = 'Application Configuration';

    protected function getHeaderActions(): array
    {
        return [
            $this->getNextStepAction(DatabaseInstallationPage::getRoutePath(), 'Next')
        ];
    }

}
