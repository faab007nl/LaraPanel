<?php

namespace App\Filament\Installer\Pages;

use App\Filament\Installer\BaseInstallerStepPage;
use Filament\Pages\Page;
use Livewire\Attributes\On;

class SuccessPage extends Page
{
    use BaseInstallerStepPage;

    protected static string $view = 'filament.installer.pages.success-page';


    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $title = 'Setup Completed';

    #[On('nextStep')]
    public function onNextStepButtonClicked(): void
    {
        $setupManager = app('manager.setup');
        $setupManager->markAsInstalled();

        $this->redirect('/');
    }

}
