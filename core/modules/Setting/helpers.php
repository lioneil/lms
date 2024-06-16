<?php

if (! function_exists('settings')) {
    /**
     * Helper function to get values from the settings table.
     *
     * @param  string  $key
     * @param  string  $default
     * @param  boolean $serialized
     * @return mixed
     */
    function settings($key = null, $default = null, $serialized = false)
    {
        if (is_null($key)) {
            return app('settings');
        }

        if (is_array($key)) {
            return app('settings')->set($key);
        }

        return app('settings')->get($key, $default, $serialized);
    }
}
