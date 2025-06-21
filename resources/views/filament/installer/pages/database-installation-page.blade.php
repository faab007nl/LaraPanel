<x-filament-panels::page>
    <div>
        <livewire:installer.install-database />

        <livewire:installer.installer-step-buttons
            previous-step-url="{{ \App\Filament\Installer\Pages\DatabaseConfigurationPage::getRoutePath() }}"
            next-step-url="{{ \App\Filament\Installer\Pages\ApplicationConfigurationPage::getRoutePath() }}"
            :next-step-disabled="true"
        />
    </div>
</x-filament-panels::page>
