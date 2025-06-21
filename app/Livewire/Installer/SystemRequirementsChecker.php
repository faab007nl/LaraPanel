<?php

namespace App\Livewire\Installer;

use App\Managers\SshManager;
use Barryvdh\Debugbar\Facades\Debugbar;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class SystemRequirementsChecker extends Component
{
    private SshManager $sshManager;

    public function mount(): void
    {
        $this->dispatch('livewire:mount');
    }

    public function render(): View
    {
        return view('livewire.installer.system-requirements-checker');
    }

    #[On('get-requirement')]
    public function getRequirement($id): void
    {
        if (!$id) {
            Debugbar::error('Requirement name is missing.');
            return;
        }

        $this->sshManager = app('manager.ssh');

        switch ($id){
            case "ssh-connection":
                $currentValue = $this->getSshConnectionUser();
                $status = $this->getSshConnectionStatus();
                break;
            case "php-version":
                $currentValue = $this->getPhpVersion();
                $status = $this->getPhpVersionStatus();
                break;
            case "php-extensions":
                $currentValue = $this->getCurrentPhpExtensionsText();
                $status = $this->getPhpExtensionsStatus();
                break;
            case "database":
                $currentValue = $this->getCurrentDatabaseText();
                $status = $this->getCurrentDatabaseStatus();
                break;
            case "nginx":
                $currentValue = $this->getNginxVersion();
                $status = $this->getNginxVersionStatus();
                break;
            default:
                Debugbar::error("Unknown requirement ID: $id");
                return;
        }

        $this->dispatch('update-requirement', [
            'id' => $id,
            'currentValue' => $currentValue,
            'status' => $status
        ]);
    }


    // Check if SSH connection is established and return the username
    public function getSshConnectionUser(): string
    {
        try{
            return trim($this->sshManager->sendCommand("whoami"));
        }catch (\Exception $e) {
            Debugbar::error('SSH Connection Error: ' . $e->getMessage());
            return "failed";
        }
    }

    public function getSshConnectionStatus(): string
    {
        $username = $this->getSshConnectionUser();
        return $username == "larapanel" ? "passed" : "failed";
    }


    // Check if the required PHP extensions are installed
    public function getPhpVersion(): string
    {
        try {
            $version = trim($this->sshManager->sendCommand("php -v"));
            preg_match('/PHP (\d+\.\d+\.\d+)/', $version, $matches);

            if (isset($matches[1])) {
                return 'PHP ' . $matches[1];
            } else {
                return 'PHP version not found';
            }
        } catch (\Exception $e) {
            Debugbar::error('PHP Version Check Error: ' . $e->getMessage());
            return "failed";
        }
    }

    public function getPhpVersionStatus(): string
    {
        $version = $this->getPhpVersion();
        return str_contains($version, 'PHP 8.') ? "passed" : "failed";
    }

    // Check if the required PHP extensions are installed
    public function getRequiredPhpExtensions(): array
    {
        return [
            'mysql',
            'curl',
            'gd',
            'mbstring',
            'xml',
            'zip',
            'bcmath',
            'json',
            'tokenizer',
            'openssl'
        ];
    }

    public function getCurrentPhpExtensions(): array
    {
        try {
            $output = strtolower(trim($this->sshManager->sendCommand("php -m")));

            $installedExtensions = [];
            $missingExtensions = [];

            $requiredExtensions = $this->getRequiredPhpExtensions();
            foreach ($requiredExtensions as $extension){
                // check if output contains the extension
                if (!str_contains($output, $extension)) {
                    $missingExtensions[] = $extension;
                }else{
                    $installedExtensions[] = $extension;
                }
            }

            return [
                'installed' => $installedExtensions,
                'missing' => $missingExtensions
            ];
        } catch (\Exception $e) {
            Debugbar::error('Installed Extensions Check Error: ' . $e->getMessage());
            return [
                'installed' => [],
                'missing' => $this->getRequiredPhpExtensions()
            ];
        }
    }

    public function getCurrentPhpExtensionsText(): string
    {
        $currentExtensions = $this->getCurrentPhpExtensions();
        $missing = implode(', ', $currentExtensions['missing']);

        if (empty($currentExtensions['missing'])) {
            return "All required PHP extensions are installed.";
        }else{
            return "Missing:<br> $missing <br><br>";
        }
    }


    public function getPhpExtensionsStatus(): string
    {
        $currentExtensions = $this->getCurrentPhpExtensions();
        if (empty($currentExtensions['missing'])) {
            return "passed";
        } else {
            return "failed";
        }
    }


    // Check if the required database is installed
    public function getCurrentDatabase(): array
    {
        try {
            $output = strtolower(trim($this->sshManager->sendCommand("mysql -V")));

            $databaseInfo = [
                'type' => 'Unknown',
                'version' => 'N/A',
                'raw_output' => $output, // Useful for debugging
            ];

            // Check for MariaDB first, as its output often contains "mysql" as well
            if (str_contains($output, 'mariadb')) {
                $databaseInfo['type'] = 'MariaDB';
                // Regex for MariaDB version: captures digits and dots after "distrib"
                if (preg_match('/distrib (\d+\.\d+\.\d+)/', $output, $matches)) {
                    $databaseInfo['version'] = $matches[1];
                }
            } elseif (str_contains($output, 'mysql')) {
                $databaseInfo['type'] = 'MySQL';
                // Regex for MySQL version: captures digits and dots after "ver"
                if (preg_match('/ver (\d+\.\d+\.\d+)/', $output, $matches)) {
                    $databaseInfo['version'] = $matches[1];
                }
            } else {
                $databaseInfo['type'] = 'Database not found';
            }

            return $databaseInfo;
        } catch (Exception $e) {
            // Using Log::error as a general example, replace with Debugbar::error if preferred
            Log::error('MySQL Version Check Error: ' . $e->getMessage());
            return [
                'type' => 'Error',
                'version' => 'N/A',
                'raw_output' => "failed",
            ];
        }
    }

    public function getCurrentDatabaseText(): string
    {
        $db = $this->getCurrentDatabase();
        if ($db['type'] === 'Database not found' || $db['type'] === 'Error') {
            return $db['raw_output'];
        }

        return "Database Type: {$db['type']}<br>Version: {$db['version']}";
    }

    public function getCurrentDatabaseStatus(): string
    {
        $db = $this->getCurrentDatabase();
        $validDatabases = ['mariadb', 'mysql'];
        $dbType = strtolower($db['type']);

        $minVersionMariaDb = '10.3.0';
        $minVersionMySql = '5.7.0';

        if (in_array($dbType, $validDatabases)) {
            if ($dbType === 'mariadb' && version_compare($db['version'], $minVersionMariaDb, '>=')) {
                return "passed";
            } elseif ($dbType === 'mysql' && version_compare($db['version'], $minVersionMySql, '>=')) {
                return "passed";
            }
        }
        return "failed";
    }

    // Check nginx
    public function getNginxVersion(): string
    {
        try {
            $version = trim($this->sshManager->sendCommand("nginx -v 2>&1"));
            preg_match('/nginx\/(\d+\.\d+\.\d+)/', $version, $matches);

            if (isset($matches[1])) {
                return 'Nginx ' . $matches[1];
            } else {
                return 'Nginx version not found';
            }
        } catch (\Exception $e) {
            Debugbar::error('Nginx Version Check Error: ' . $e->getMessage());
            return "failed";
        }
    }

    public function getNginxVersionStatus(): string
    {
        $version = $this->getNginxVersion();
        $minVersion = '1.18.0';

        if (str_contains($version, 'Nginx')) {
            preg_match('/(\d+\.\d+\.\d+)/', $version, $matches);
            if (isset($matches[1]) && version_compare($matches[1], $minVersion, '>=')) {
                return "passed";
            }
        }
        return "failed";
    }


}
