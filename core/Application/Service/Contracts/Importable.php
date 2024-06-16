<?php

namespace Core\Application\Service\Contracts;

interface Importable
{
    /**
     * Import from array, file, or any resource.
     *
     * @param  array|mixed $attributes
     * @return void
     */
    public function import($attributes);
}
