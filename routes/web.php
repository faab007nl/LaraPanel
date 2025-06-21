<?php


use App\Filament\Installer\Pages\WelcomePage;
use App\Providers\Filament\InstallerPanelProvider;

Route::get('/installer', function () {
    return redirect()->to(InstallerPanelProvider::getUrl() . '/' . WelcomePage::getRoutePath());
})->name('installer');
