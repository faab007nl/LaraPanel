<?php


use App\Filament\Installer\Pages\WelcomePage;
use App\Providers\Filament\InstallerPanelProvider;
use App\Providers\Filament\SitesPanelProvider;

Route::get('/', function () {
    return redirect()->to(SitesPanelProvider::getUrl());
})->name('sites');
Route::get('/installer', function () {
    return redirect()->to(InstallerPanelProvider::getUrl() . '/' . WelcomePage::getRoutePath());
})->name('installer');
