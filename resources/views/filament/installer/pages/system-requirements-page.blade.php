<x-filament-panels::page>
    <div>
        <livewire:installer.system-requirements-checker />

        <livewire:installer.installer-step-buttons
            previous-step-url="{{ \App\Filament\Installer\Pages\WelcomePage::getRoutePath() }}"
            next-step-url="{{ \App\Filament\Installer\Pages\LicenseAgreementPage::getRoutePath() }}"
            :next-step-disabled="true"
        />
    </div>
</x-filament-panels::page>
