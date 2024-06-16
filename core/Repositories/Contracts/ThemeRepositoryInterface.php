<?php

namespace Core\Repositories\Contracts;

interface ThemeRepositoryInterface extends AssetRepositoryInterface
{
    /**
     * Retrieve a metadata of theme from it's manifest.
     *
     * @param  string $key
     * @param  string $default
     * @return string
     */
    public function detail($key, $default = null);

    /**
     * Retrieve the theme's version.
     *
     * @return string
     */
    public function version();
}
