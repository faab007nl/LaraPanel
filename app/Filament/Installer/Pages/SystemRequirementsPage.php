<?php

namespace App\Filament\Installer\Pages;

use App\Filament\Installer\BaseInstallerStepPage;
use Filament\Pages\Page;

class SystemRequirementsPage extends Page
{
    use BaseInstallerStepPage;

    protected static string $view = 'filament.installer.pages.system-requirements-page';


    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $title = 'System Requirements';

    protected function getHeaderActions(): array
    {
        return [
            $this->getNextStepAction(LicenseAgreementPage::getRoutePath(), 'Next')
        ];
    }

}
