<?php

namespace Core\Support\Traits;

trait Randomizer
{
    /**
     * Generate a random string from a given length.
     *
     * @param  integer $length
     * @return string
     */
    public function randomize(int $length = 6): string
    {
        return (string) bin2hex(openssl_random_pseudo_bytes($length/2));
    }
}
