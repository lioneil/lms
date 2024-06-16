<?php

namespace Tests;

trait WithForeignKeys
{
    /**
     * Enables foreign keys.
     *
     * @return void
     */
    public function enableForeignKeys()
    {
        $db = $this->app->make('db');
        $db->getSchemaBuilder()->enableForeignKeyConstraints();
    }
}
