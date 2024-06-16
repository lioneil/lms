<?php

namespace Core\Enumerations;

abstract class NameStandard
{
    const FIRSTNAME = 'firstname';
    const FULLNAME = 'firstname md lastname';
    const LASTNAME = 'lastname';
    const MIDDLEINITIAL = 'md';
    const MIDDLENAME = 'middlename';
    const OFFICIALNAME = 'prefixname firstname md lastname';
    const PREFIXNAME = 'prefixname';
    const PROPERNAME = 'lastname, firstname md';
    const SUFFIXNAME = 'suffixname';
    const USERNAME = 'username';
}
