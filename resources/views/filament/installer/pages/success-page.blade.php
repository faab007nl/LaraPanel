<x-filament-panels::page>
    <div>

        <p class="mb-4">Congratulations! Larapanel has been successfully installed on your server.</p>
        <p class="mb-4">You can now access the Larapanel dashboard and start managing your applications.</p>
        <p class="mb-4">Thank you for choosing Larapanel!</p>

        <p class="mb-4">If you have any questions or need assistance, please refer to the <a href="https://docs.larapanel.com" target="_blank" class="text-blue-500 underline">Larapanel documentation</a>.</p>

        <livewire:installer.installer-step-buttons
            previous-step-url="{{ \App\Filament\Installer\Pages\ApplicationConfigurationPage::getRoutePath() }}"
            next-step-url="#"
            next-step-label="Finish Installation"
        />
    </div>
</x-filament-panels::page>
