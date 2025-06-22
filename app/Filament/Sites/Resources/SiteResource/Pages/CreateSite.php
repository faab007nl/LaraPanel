<?php

namespace App\Filament\Sites\Resources\SiteResource\Pages;

use App\Filament\Sites\Resources\SiteResource;
use Barryvdh\Debugbar\Facades\Debugbar;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSite extends CreateRecord
{

    protected static string $resource = SiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getCancelFormAction(),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }


    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())($data);

        $record->save();

        return $record;
    }


    public function cancelCustom(): void
    {
        redirect()->route('sites');
    }

}
