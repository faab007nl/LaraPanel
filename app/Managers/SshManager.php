<?php

namespace App\Managers;

use Exception;
use phpseclib3\Net\SSH2;

class SshManager
{

    private string $host;
    private int $port;
    private string $username;
    private string $password;

    private SSH2 $ssh;

    public function __construct()
    {
        $this->host = config('services.ssh.host');
        $this->port = config('services.ssh.port');
        $this->username = config('services.ssh.username');
        $this->password = config('services.ssh.password');

        $this->ssh = new SSH2($this->host, $this->port);
    }

    /**
     * @throws Exception
     */
    public function login(): void
    {
        if (!$this->ssh->login($this->username, $this->password)) {
            throw new Exception('SSH login failed');
        }
    }

    public function logout(): void
    {
        $this->ssh->disconnect();
    }

    /**
     * @throws Exception
     */
    private function checkLogin(): void
    {
        if (!$this->ssh->isConnected()) {
            $this->login();
        }
    }

    /**
     * @throws Exception
     */
    public function sendCommand(string $command): string
    {
        $this->checkLogin();

        return $this->ssh->exec($command);
    }

}
