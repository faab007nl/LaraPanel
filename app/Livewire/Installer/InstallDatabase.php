<?php

namespace App\Livewire\Installer;

use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Livewire\Component;

class InstallDatabase extends Component
{

    public string $installStatus = "idle";
    public string $errorMessage = "";

    public function render(): View
    {
        return view('livewire.installer.install-database');
    }

    public function installDatabase(): void
    {
        $this->installStatus = "installing";

        try{
            Artisan::call('migrate:fresh --seed');
            $output = Artisan::output();

            Debugbar::debug($output);

            if ($output) {
                $this->installStatus = "success";
                $this->dispatch('nextButtonEnable');
            } else {
                $this->installStatus = "error";
                $this->errorMessage = "No output from migration command.";
            }
        }catch (\Throwable $e) {
            $this->installStatus = "error";
            $this->errorMessage = $e->getMessage();
        }

    }

}
