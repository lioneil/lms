<?php

namespace Core\Repositories;

use Illuminate\Config\Repository as ConfigRepository;

class LanguageRepository extends ConfigRepository
{
    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Array of supported locales.
     *
     * @var array
     */
    protected $supported = [];

    /**
     * Create a new configuration repository.
     *
     * @param  array $items
     * @param  array $supported
     * @return void
     */
    public function __construct(array $items = [], array $supported = [])
    {
        $this->items = array_merge($this->items, $items);
        $this->supported = $supported;
    }

    /**
     * Retrieve array of supported locales.
     *
     * @return array
     */
    public function supported(): array
    {
        return $this->supported;
    }
}
