<?php

namespace App\Livewire\Installer;

use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class InstallerStepButtons extends Component
{

    public string|null $previousStepUrl = null;
    public string $previousStepLabel = 'Previous';

    public string|null $nextStepUrl = null;
    public string $nextStepLabel = 'Next';

    public bool $nextStepDisabled = false;

    public function render(): View
    {
        if(session()->has('nextButtonEnable')){
            $this->nextStepDisabled = false;
            session()->forget('nextButtonEnable');
        }
        return view('livewire.installer.installer-step-buttons');
    }

    private function renderActions(): string
    {
        $previousStepAction = Action::make($this->previousStepLabel)
            ->icon('heroicon-o-arrow-left')
            ->size(ActionSize::ExtraLarge)
            ->hidden($this->previousStepUrl === null)
            ->action('handleNextStepEvent')
            ->color('secondary');
        $nextStepAction = Action::make($this->nextStepLabel)
            ->icon('heroicon-o-arrow-right')
            ->size(ActionSize::ExtraLarge)
            ->hidden($this->nextStepUrl === null)
            ->action('handleNextStepAction')
            ->disabled($this->nextStepDisabled)
            ->color('primary');

        $actionsHtml = collect([$previousStepAction, $nextStepAction])
            ->map(function ($action) {
                /** @var Action $action */
                if($action->isHidden()){
                    return "<a></a>";
                }
                return $action->toHtml();
            })
            ->implode('');
        return <<<HTML
            <div class="flex justify-between space-x-2 mt-5">
                $actionsHtml
            </div>
            HTML;
    }

    #[On('handleNextStepEvent')]
    public function handleNextStepEvent(): void
    {
        $this->dispatch('previousStep');

        // Now, perform the redirect AFTER your logic
        if ($this->previousStepUrl) {
            $this->redirect($this->previousStepUrl);
        }
    }

    #[On('handleNextStepAction')]
    public function handleNextStepAction(): void
    {
        $this->dispatch('nextStep');

        // Now, perform the redirect AFTER your logic
        if ($this->nextStepUrl) {
            $this->redirect($this->nextStepUrl);
        }
    }

    #[On('nextButtonEnable')]
    public function onNextButtonEnableEvent(): void
    {
        $this->nextStepDisabled = false;
        $this->dispatch('nextStepEnabled');
    }

    #[On('nextButtonDisable')]
    public function onNextButtonDisableEvent(): void
    {
        $this->nextStepDisabled = true;
        $this->dispatch('nextStepDisabled');
    }

}
