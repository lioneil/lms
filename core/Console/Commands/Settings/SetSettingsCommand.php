<?php

namespace Core\Console\Commands\Settings;

use Illuminate\Console\Command;
use Setting\Services\SettingServiceInterface;

class SetSettingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:set
                {keys* : The key-value pairs to be saved, format: key1=value1 key2=value2...}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a key-value pair to the settings database.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            foreach ($this->argument('keys') as $keys) {
                $key = $this->parseString($keys, 0);
                $value = $this->parseString($keys, 1);
                settings([$key => $value])->save();
                $this->info("key {$key} with value '{$value}' saved to settings database.");
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Parse the string and return the specified index.
     *
     * @param  string  $string
     * @param  integer $i
     * @return string
     */
    protected function parseString($string, $i = 0)
    {
        return explode('=', $string)[$i] ?? null;
    }
}
