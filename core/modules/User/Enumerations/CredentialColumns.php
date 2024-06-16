<?php

namespace User\Enumerations;

abstract class CredentialColumns
{
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const USERNAME = 'username';

    /**
     * Retrieve all columns.
     *
     * @return array
     */
    public static function all()
    {
        $reflector = new \ReflectionClass(__CLASS__);

        return $reflector->getConstants();
    }
}
