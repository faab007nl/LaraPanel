<?php

namespace App\Filament\Installer\Pages;

use App\Filament\Installer\BaseInstallerStepPage;
use Filament\Forms\Form;
use Filament\Pages\Page;

class DatabaseConfigurationPage extends Page
{
    use BaseInstallerStepPage;

    protected static string $view = 'filament.installer.pages.form-page';


    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $title = 'Database Configuration';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getNextStepAction(ApplicationConfigurationPage::getRoutePath(), 'Next')
        ];
    }

}
