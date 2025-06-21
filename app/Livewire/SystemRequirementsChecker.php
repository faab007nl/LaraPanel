<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class SystemRequirementsChecker extends Component
{

    public function render(): View
    {
        return view('livewire.system-requirements-checker');
    }


    public function getPHPStatus(): string
    {
        return version_compare(phpversion(), '8.0', '>=') ? 'Passed' : 'Failed';
    }

    public function getOpenSslStatus(): string
    {
        return extension_loaded('openssl') ? 'Passed' : 'Failed';
    }

    public function getCurlStatus(): string
    {
        return extension_loaded('curl') ? 'Passed' : 'Failed';
    }

    public function getMariaDBStatus(): string
    {
        $version = shell_exec('mysql -V 2>&1');
        if (str_contains($version, 'MariaDB') || str_contains($version, 'MySQL')) {
            preg_match('/[0-9]+\.[0-9]+/', $version, $matches);
            if (isset($matches[0]) && version_compare($matches[0], '10.3', '>=')) {
                return 'Passed';
            }
        }
        return 'Failed';
    }

    public function getOpenSshStatus(): string
    {
        $version = shell_exec('ssh -V 2>&1');
        return str_contains($version, 'OpenSSH') && version_compare($version, '7.0', '>=') ? 'Passed' : 'Failed';
    }

    public function getNginxStatus(): string
    {
        $version = shell_exec('nginx -v 2>&1');
        return str_contains($version, 'nginx') && version_compare($version, '1.18', '>=') ? 'Passed' : 'Failed';
    }

    public function getLaraPanelUserStatus(): string
    {
        $userExists = shell_exec('id -u larapanel');
        return $userExists ? 'Passed' : 'Failed';
    }

}
