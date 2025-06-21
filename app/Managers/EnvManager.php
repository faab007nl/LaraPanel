<?php

namespace App\Managers;

use Exception;
use Illuminate\Support\Facades\Artisan;

class EnvManager
{

    /**
     * Update an environment variable.
     *
     * @param string $key The environment variable key.
     * @param string $value The new value for the key.
     * @return bool True if successful, false otherwise.
     * @throws Exception
     */
    public function updateEnv(string $key, string $value): bool
    {
        $path = base_path('.env');

        if (!file_exists($path)) {
            throw new Exception('.env file not found.');
        }

        if (!is_writable($path)) {
            throw new Exception('.env file is not writable. Check permissions.');
        }

        try {
            $oldEnv = file_get_contents($path);
            $newEnv = $oldEnv;

            // Prepare the new value for writing.
            // If the value contains spaces, quotes, or special characters,
            // it should be wrapped in double quotes.
            $escapedValue = preg_match('/\s|"|\'|\\\#|=/', $value) ? '"' . addcslashes($value, '"') . '"' : $value;

            // Check if the key already exists
            if (preg_match("/^{$key}=.*/m", $oldEnv)) {
                // Update existing key
                $newEnv = preg_replace("/^{$key}=.*/m", "{$key}={$escapedValue}", $oldEnv);
            } else {
                // Add new key at the end of the file
                $newEnv .= "\n{$key}={$escapedValue}";
            }

            if (file_put_contents($path, $newEnv) === false) {
                return false;
            }

            // Clear the config cache
            Artisan::call('config:clear');
            Artisan::call('cache:clear'); // Often good practice to clear cache as well

            return true;
        } catch (Exception $e) {
            // Log the error or handle it as appropriate
            report($e);
            return false;
        }
    }

    /**
     * Get the value of an environment variable.
     *
     * @param string $key The environment variable key.
     * @return string|null The value of the environment variable, or null if not found.
     */
    public function getEnv(string $key): ?string
    {
        $path = base_path('.env');

        if (!file_exists($path)) {
            return null;
        }

        $envContent = file_get_contents($path);
        if ($envContent === false) {
            return null;
        }

        // Use a regex to find the key and return its value
        if (preg_match("/^{$key}=(.*)$/m", $envContent, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

}
