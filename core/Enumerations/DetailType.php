<?php

namespace Core\Enumerations;

abstract class DetailType
{
    const LIKE = 1;
    const DISLIKE = 0;
    const ACCOUNT = 'account';
    const BIODATA = 'biodata';
    const FAVORITE = 'favorite';
    const DETAIL = 'detail';
    const BIRTHDAY = 'birthday';
    const DEFAULT_ICON = 'mdi-square-edit-outline';

    /**
     * Retrieve all sensitive detail types.
     *
     * @return array
     */
    public static function sensitive(): array
    {
        return [
            self::ACCOUNT,
        ];
    }
}
