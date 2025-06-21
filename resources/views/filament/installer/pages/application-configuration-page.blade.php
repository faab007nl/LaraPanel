<x-filament-panels::page>
    <div>
        <div class="filament-form">
            {{ $this->form }}
        </div>

        <livewire:installer.installer-step-buttons
            previous-step-url="{{ \App\Filament\Installer\Pages\DatabaseInstallationPage::getRoutePath() }}"
            next-step-url="{{ \App\Filament\Installer\Pages\SuccessPage::getRoutePath() }}"
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
