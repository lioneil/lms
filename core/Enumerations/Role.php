<?php

namespace Core\Enumerations;

abstract class Role
{
    const ADMIN = 'admin';
    const DEV = 'dev';
    const ROOT = 'root';
    const SUPER_ADMIN = 'super-admin';
    const SUPERADMIN = 'superadmin';
    const TESTER = 'tester';
    const TEST = 'test';

    /**
     * Retrieve all superadmin codes.
     *
     * @return array
     */
    public static function superadmins(): array
    {
        return [
            self::DEV,
            self::ROOT,
            self::SUPER_ADMIN,
            self::SUPERADMIN,
        ];
    }
}
