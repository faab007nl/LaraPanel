<?php

namespace App\Managers\Installer;

class InstalledManager
{

    private string $installedFilePath;

    public function __construct()
    {
        $this->installedFilePath = storage_path('installed.json');

        if (!file_exists($this->installedFilePath)) {
            file_put_contents($this->installedFilePath, json_encode([
                'installed' => false
            ]));
        }
    }

    public function isInstalled(): bool
    {
        $json = file_exists($this->installedFilePath);
        if (!$json) {
            return false;
        }
        $content = file_get_contents($this->installedFilePath);
        $data = json_decode($content, true);
        return isset($data['installed']) && $data['installed'] === true;
    }

    public function markAsInstalled(): void
    {
        file_put_contents($this->installedFilePath, json_encode([
            'installed' => true
        ]));
    }

    public function markAsUninstalled(): void
    {
        if ($this->isInstalled()) {
            unlink($this->installedFilePath);
        }
    }

}
