<x-filament-panels::page>
    <div>

        <livewire:installer.installer-step-buttons
            previous-step-url="{{ \App\Filament\Installer\Pages\ApplicationConfigurationPage::getRoutePath() }}"
            next-step-url="{{ \App\Filament\Installer\Pages\SuccessPage::getRoutePath() }}"
            :next-step-disabled="true"
        />
    </div>
</x-filament-panels::page>
