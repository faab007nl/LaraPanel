<?php

namespace App\Filament\Installer\Pages;

use App\Filament\Installer\BaseInstallerStepPage;
use Filament\Actions\Action;
use Filament\Pages\Page;

class SuccessPage extends Page
{
    use BaseInstallerStepPage;

    protected static string $view = 'filament.installer.pages.success-page';


    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $title = 'Setup Complete';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Complete Setup')
                ->action(function (): void {
                    $installedManager = app('installed.manager');
                    $installedManager->markAsInstalled();

                    $this->redirect("/");
                })
                ->icon('heroicon-o-arrow-right')
                ->color('primary')
        ];
    }

}
