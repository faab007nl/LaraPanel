<?php

namespace App\Filament\Installer\Pages;

use App\Filament\Installer\BaseInstallerStepPage;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class ApplicationConfigurationPage extends Page
{
    use BaseInstallerStepPage;

    protected static string $view = 'filament.installer.pages.application-configuration-page';


    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $title = 'Application Configuration';

    public ?array $data = [];

    public function __construct()
    {
        // session has success
        if (session()->has('success')) {
            session()->forget('success');
            Notification::make()
                ->title('Application configured successfully!')
                ->body('Environment variables updated and application configured.')
                ->success()
                ->send();
            session()->put('nextButtonEnable', true);
        }
        // session has error
        if (session()->has('error')) {
            $errorMessage = session()->has('errorMessage') ? session()->get('errorMessage') : null;
            session()->forget('error');
            session()->forget('errorMessage');

            Notification::make()
                ->title('Failed to configure application')
                ->body('Could not configure the application. <br><br> ' . ($errorMessage ?? 'Please check your inputs and try again.'))
                ->danger()
                ->send();
        }
    }

    public function form(Form $form): Form
    {
        $envManager = app('manager.env');
        $currentDomain = str_replace(['http://', 'https://'], '', $envManager->getEnv('APP_URL'));

        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Create Admin')
                        ->schema([
                            TextInput::make('username')
                                ->label('Username')
                                ->default('admin')
                                ->disabled()
                                ->required(),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->default('mail@example.com')
                                ->required(),
                            TextInput::make('firstname')
                                ->label('Firstname')
                                ->default('Admin')
                                ->required(),
                            TextInput::make('lastname')
                                ->label('Lastname')
                                ->default('Admin')
                                ->required(),
                            TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->revealable()
                                ->default('admin123')
                                ->required(),
                        ]),
                    Wizard\Step::make('Setup Domain')
                        ->schema([
                            TextInput::make('domain')
                                ->label('Domain')
                                ->placeholder('example.com')
                                ->required()
                                ->maxLength(255)
                                ->prefix('https://')
                                ->default($currentDomain)
                                ->helperText('The domain where your application will be accessible.'),
                        ]),
                    Wizard\Step::make('Email Configuration')
                        ->schema([
                            TextInput::make('mail_host')
                                ->label("Email Host")
                                ->default($envManager->getEnv('MAIL_HOST'))
                                ->required(),
                            TextInput::make('email_port')
                                ->label('Email Port')
                                ->numeric()
                                ->default($envManager->getEnv('MAIL_PORT'))
                                ->required(),

                            TextInput::make('mail_username')
                                ->label("Email Username")
                                ->default($envManager->getEnv('MAIL_USERNAME'))
                                ->required(),
                            TextInput::make('mail_password')
                                ->label("Email Password")
                                ->default($envManager->getEnv('MAIL_PASSWORD'))
                                ->required(),
                            TextInput::make('mail_from_address')
                                ->label("Email From Address")
                                ->default($envManager->getEnv('MAIL_FROM_ADDRESS'))
                                ->required(),
                            TextInput::make('mail_from_name')
                                ->label("Email From Name")
                                ->default($envManager->getEnv('MAIL_FROM_NAME'))
                                ->required(),
                        ]),
                ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button type="button" wire:click="submit">
                        Save Configuration
                    </x-filament::button>
                    BLADE
                )))->startOnStep(session()->has('nextButtonEnable') ? 3 : 1),
            ])
            ->statePath('data');
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function submit(): void
    {
        $username = $this->data['username'] ?? '';
        $email = $this->data['email'] ?? '';
        $firstname = $this->data['firstname'] ?? '';
        $lastname = $this->data['lastname'] ?? '';
        $password = $this->data['password'] ?? '';

        $domain = $this->data['domain'] ?? '';

        $mailHost = $this->data['mail_host'] ?? '';
        $emailPort = $this->data['email_port'] ?? '';
        $mailUsername = $this->data['mail_username'] ?? '';
        $mailPassword = $this->data['mail_password'] ?? '';
        $mailFromAddress = $this->data['mail_from_address'] ?? '';
        $mailFromName = $this->data['mail_from_name'] ?? '';

        try {
            // Validate the inputs
            $validator = validator([
                'username' => $username,
                'email' => $email,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'password' => $password,
                'domain' => $domain,
                'mail_host' => $mailHost,
                'email_port' => $emailPort,
                'mail_username' => $mailUsername,
                'mail_password' => $mailPassword,
                'mail_from_address' => $mailFromAddress,
                'mail_from_name' => $mailFromName,
            ], [
                'username' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'password' => 'required|string|min:8',
                'domain' => 'required|string|max:255',
                'mail_host' => 'required|string|max:255',
                'email_port' => 'required|numeric|min:1|max:65535',
                'mail_username' => 'required|string|max:255',
                'mail_password' => 'required|string|max:255',
                'mail_from_address' => 'required|email|max:255',
                'mail_from_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                Notification::make()
                    ->title('Validation Error')
                    ->body($validator->errors()->first())
                    ->danger()
                    ->send();
                return;
            }

            // Save the user
            $user = User::query()
                ->orWhere('username', $username)
                ->first();
            if(!$user){
                $user = new User();
            }
            $user->username = $username;
            $user->email = $email;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->password = bcrypt($password);
            $user->role = 'admin'; // Assuming 'admin' is a valid role
            $user->save();

            // Update the environment app url
            $envManager = app('manager.env');
            $envManager->updateEnv('APP_URL', 'https://' . $domain);

            // Update the email config
            $envManager->updateEnv('MAIL_MAILER', 'smtp');
            $envManager->updateEnv('MAIL_HOST', $mailHost);
            $envManager->updateEnv('MAIL_PORT', $emailPort);
            $envManager->updateEnv('MAIL_USERNAME', $mailUsername);
            $envManager->updateEnv('MAIL_PASSWORD', $mailPassword);
            $envManager->updateEnv('MAIL_FROM_ADDRESS', $mailFromAddress);
            $envManager->updateEnv('MAIL_FROM_NAME', $mailFromName);

            session()->put('success', true);
            $this->dispatch('reloadPage');
        } catch (\Exception $e) {

            // delete user
            try{
                $user = User::query()
                    ->where('email', $email)->first();
                if($user) {
                    $user->delete();
                }
            }catch (\Exception $e) {
                // ignore errors while deleting user
            }

            session()->put('error', true);
            session()->put('errorMessage', $e->getMessage());
            $this->dispatch('reloadPage');
            return;
        }
    }

}
