<?php

namespace Database\Connection;

interface Connection
{
    /**
     * Gets an instance of the Connection object.
     *
     * @return Connection
     */
    public static function getInstance(): Connection;

    /**
     * Executes a given database query string. Returns a PDOStatement object or
     * null if the query fails.
     */
    public function execute($query);
}
