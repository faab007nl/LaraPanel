<x-filament-panels::page>
    <div>
        <div class="filament-form database-form">
            {{ $this->form }}

            <div class="flex items-center justify-center">
                <x-filament::button type="button" wire:click="submit">
                    Check Database Connection
                </x-filament::button>
            </div>
        </div>

        <livewire:installer.installer-step-buttons
            previous-step-url="{{ \App\Filament\Installer\Pages\LicenseAgreementPage::getRoutePath() }}"
            next-step-url="{{ \App\Filament\Installer\Pages\DatabaseInstallationPage::getRoutePath() }}"
            :next-step-disabled="true"
        />
    </div>

    @script
    <script>
        Livewire.on('reloadPage', () => {
            location.reload();
        });
    </script>
    @endscript
</x-filament-panels::page>
