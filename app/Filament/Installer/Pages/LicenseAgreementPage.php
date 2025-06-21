<?php

namespace App\Filament\Installer\Pages;

use App\Filament\Installer\BaseInstallerStepPage;
use Filament\Pages\Page;

class LicenseAgreementPage extends Page
{
    use BaseInstallerStepPage;

    protected static string $view = 'filament.installer.pages.license-agreement-page';


    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'License Agreement';

    protected function getHeaderActions(): array
    {
        return [
            $this->getNextStepAction(DatabaseConfigurationPage::getRoutePath(), 'Next')
        ];
    }

}
