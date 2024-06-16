<?php

namespace Setting\Enumerations;

abstract class SettingsKey
{
    const APP_AUTHOR = 'app:author';
    const APP_COPYRIGHT = 'app:copyright';
    const APP_DEV = 'app:developer';
    const APP_EMAIL = 'app:email';
    const APP_LOGO = 'app:logo';
    const APP_SUBTITLE = 'app:subtitle';
    const APP_TAGLINE = 'app:tagline';
    const APP_THEME = 'app:theme';
    const APP_TITLE = 'app:title';
    const APP_YEAR = 'app:year';
    const APP_VERSION = 'app:version';
    const APP_ENVIRONMENT = 'app:environment';
    const APP_TIMEZONE = 'app:timezone';
    const APP_KEY = 'app:key';
    const APP_DEBUG = 'app:debug';
    const APP_MODULE = 'app:module';
    const APP_CONNECTION = 'app:connection';
    const APP_HOST = 'app:host';
    const APP_PORT = 'app:port';
    const APP_DATABASE = 'app:database';
    const APP_USERNAME = 'app:username';
    const APP_MAIL_ENVIRONMENT = 'app:mail';
    const APP_MAIL_HOST = 'app:mailhost';
    const APP_MAIL_PORT= 'app:mailport';
    const APP_ENCRYPTION = 'app:encryption';
    const APP_SERVER_SOFTWARE = 'app:serversoftware';
    const APP_SERVER_ADMIN = 'app:serveradmin';
    const APP_DOCUMENT_ROOT = 'app:documentroot';
    const APP_REMOTE_ADDRESS = 'app:remoteaddress';
    const APP_PHP_VERSION = 'app:phpversion';
    const APP_MAXIMUM_FILE_UPLOADS = 'app:maxfileuploads';
    const APP_MAXIMUM_SIZE = 'app:maximumsize';
    const APP_MAX_FILE_SIZE = 'app:maxfilesize';
    const APP_OS = 'app:os';
    const APP_RELEASE_NAME = 'app:releasename';
    const APP_USER_AGENT = 'app:useragent';
    const APP_DATE = 'app:date';
    const APP_TIME = 'app:time';

    const LOGO_NAME = 'logo';

    const COMMENTING_ENABLE = 'commenting:enable';
    const BLACKLISTED_WORDS = 'blacklisted:words';
    const BLACKLISTED_EXACT = 'blacklisted:exact';
}
