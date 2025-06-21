<?php

namespace App\Filament\Installer\Pages;

use App\Filament\Installer\BaseInstallerStepPage;
use Barryvdh\Debugbar\Facades\Debugbar;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportEvents\HandlesEvents;

class DatabaseConfigurationPage extends Page implements HasForms
{
    use BaseInstallerStepPage;
    use InteractsWithForms;
    use HandlesEvents;

    protected static string $view = 'filament.installer.pages.database-configuration-page';

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $title = 'Database Configuration';

    public ?array $data = [];

    public function __construct()
    {
        // session has success
        if (session()->has('success')) {
            session()->forget('success');
            Notification::make()
                ->title('Database connection successful!')
                ->body('Environment variables updated and connection established.')
                ->success()
                ->send();
            Debugbar::debug('Database connection successful!');
            session()->put('nextButtonEnable', true);
        }
        // session has error
        if (session()->has('error')) {
            session()->forget('error');
            Notification::make()
                ->title('Database Connection Failed!')
                ->body('Could not connect to the database. Please check your credentials and try again.')
                ->danger()
                ->send();
        }
    }

    public function form(Form $form): Form
    {
        $envManager = app('manager.env');
        return $form
            ->schema([
                Grid::make()
                    ->columns(1)
                    ->schema([
                        TextInput::make('db_host')
                            ->label("Database Host")
                            ->required()
                            ->label('Database Host')
                            ->default($envManager->getEnv('DB_HOST')),
                        TextInput::make('db_port')
                            ->label('Database Port')
                            ->required()
                            ->default($envManager->getEnv('DB_PORT')),
                        TextInput::make('db_name')
                            ->label('Database Name')
                            ->required()
                            ->default($envManager->getEnv('DB_DATABASE')),
                        TextInput::make('db_user')
                            ->label('Database Username')
                            ->required()
                            ->default($envManager->getEnv('DB_USERNAME')),
                        TextInput::make('db_password')
                            ->label('Database Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->default($envManager->getEnv('DB_PASSWORD')),
                    ]),
            ])
            ->statePath('data');
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function submit(): void
    {
        $envManager = app('manager.env');

        $host = $this->data['db_host'] ?? $envManager->getEnv('DB_HOST');
        $port = $this->data['db_port'] ?? $envManager->getEnv('DB_PORT');
        $name = $this->data['db_name'] ?? $envManager->getEnv('DB_DATABASE');
        $user = $this->data['db_user'] ?? $envManager->getEnv('DB_USERNAME');
        $password = $this->data['db_password'] ?? $envManager->getEnv('DB_PASSWORD');

        try{
            $envManager->updateEnv("DB_CONNECTION", "mysql");
            $envManager->updateEnv("DB_HOST", $host);
            $envManager->updateEnv("DB_PORT", $port);
            $envManager->updateEnv("DB_DATABASE", $name);
            $envManager->updateEnv("DB_USERNAME", $user);
            $envManager->updateEnv("DB_PASSWORD", $password);

            // --- Force the new database configuration into the current runtime ---
            config([
                'database.connections.mysql.host'     => $host,
                'database.connections.mysql.port'     => $port,
                'database.connections.mysql.database' => $name,
                'database.connections.mysql.username' => $user,
                'database.connections.mysql.password' => $password,
            ]);

            // --- Test the database connection ---
            DB::purge('mysql'); // Forget any previous connection to 'mysql'
            DB::connection('mysql')->getPdo(); // Try to get a PDO instance. This will throw an exception on failure.

            session()->put('success', true);
            $this->dispatch('reloadPage');
        } catch (\Exception $e) {
            session()->put('error', true);
            $this->dispatch('reloadPage');
        }
    }

}
