<?php

namespace App\Filament\Installer\Pages;

use App\Filament\Installer\BaseInstallerStepPage;
use Filament\Pages\Page;

class PostInstallPage extends Page
{
    use BaseInstallerStepPage;

    protected static string $view = 'filament.installer.pages.post-install-page';


    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?string $title = 'Post Install';

    protected function getHeaderActions(): array
    {
        return [
            $this->getNextStepAction(SuccessPage::getRoutePath(), 'Next')
        ];
    }

}
