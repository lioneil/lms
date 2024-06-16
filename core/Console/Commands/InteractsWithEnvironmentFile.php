<?php

namespace Core\Console\Commands;

trait InteractsWithEnvironmentFile
{
    /**
     * Write a new environment file with the given key.
     *
     * @param  array $data
     * @return void
     */
    protected function writeEnvironmentFileFromArray(array $data): void
    {
        $pattern = '/([^\=]*)\=[^\n]*/';

        $envFile = base_path('.env');

        $lines = file($envFile);
        $newLines = [];
        foreach ($lines as $line) {
            preg_match($pattern, $line, $matches);
            if (! count($matches)) {
                $newLines[] = $line;
                continue;
            }
            if (! key_exists(trim($matches[1]), $data)) {
                $newLines[] = $line;
                continue;
            }
            if (strpos(trim($matches[1]), ' ') !== false) {
                $line = trim($matches[1]) . "={$data[trim($matches[1])]}\n";
            } else {
                $value = preg_match('/\s/', $data[trim($matches[1])])
                         ? "\"{$data[trim($matches[1])]}\""
                         : $data[trim($matches[1])];
                $line = trim($matches[1]) . "={$value}\n";
            }
            $newLines[] = $line;
        }

        $newContent = implode('', $newLines);
        file_put_contents($this->laravel->environmentFilePath(), $newContent);
    }
}
