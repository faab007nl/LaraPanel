<div style="display: flex; gap: 5px;">

    @php
        $adminPanelUrl = $panels['admin'];
        $sitesPanelUrl = $panels['sites'];

        $currentPanel = $currentPanel->getId();
    @endphp

    @if($currentPanel === 'sites')
        <x-filament::button
            type="button"
            :href="$adminPanelUrl"
            tag="a"
        >
            Admin Area
        </x-filament::button>
    @elseif($currentPanel === 'admin')
        <x-filament::button
            type="button"
            :href="$sitesPanelUrl"
            tag="a"
        >
            Sites Area
        </x-filament::button>
    @endif

</div>
