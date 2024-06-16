<?php

namespace User\Models\Accessors;

use Core\Enumerations\NameStandard as Names;

trait NameAttributes
{
    /**
     * Retrieve the global display name.
     *
     * @return string
     */
    public function getDisplaynameAttribute()
    {
        return strtr(settings('user:displayname', Names::FULLNAME), [
            Names::PREFIXNAME => $this->prefixname,
            Names::FIRSTNAME => $this->firstname,
            Names::MIDDLENAME => $this->middlename,
            Names::MIDDLEINITIAL => str_limit($this->middlename, 1, '.'),
            Names::LASTNAME => $this->lastname,
            Names::SUFFIXNAME => $this->suffixname,
        ]);
    }

    /**
     * Retrieve the global display name.
     *
     * @return string
     */
    public function getFullnameAttribute()
    {
        return strtr(Names::FULLNAME, [
            Names::FIRSTNAME => $this->firstname,
            Names::MIDDLEINITIAL => str_limit($this->middlename, 1, '.'),
            Names::LASTNAME => $this->lastname,
        ]);
    }

    /**
     * Retrieve the global display name.
     *
     * @return string
     */
    public function getPropernameAttribute()
    {
        return strtr(Names::PROPERNAME, [
            Names::FIRSTNAME => $this->firstname,
            Names::MIDDLEINITIAL => str_limit($this->middlename, 1, '.'),
            Names::LASTNAME => $this->lastname,
        ]);
    }

    /**
     * Retrieve the global display name.
     *
     * @return string
     */
    public function getOfficialnameAttribute()
    {
        return strtr(Names::OFFICIALNAME, [
            Names::PREFIXNAME => $this->prefixname,
            Names::FIRSTNAME => $this->firstname,
            Names::MIDDLEINITIAL => str_limit($this->middlename, 1, '.'),
            Names::LASTNAME => $this->lastname,
        ]);
    }

    /**
     * Retrieve the user's username
     * field value.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}
