<?php

namespace Database\Connection;

use Database\Connection\Connection;

class MySQLConnection implements Connection
{
    /**
     * Construct a new MySQLConnection instance.
     *
     * @param mixed[] $config A configuration array.
     */
    private function __construct($config)
    {
        $config = array_map(
            function ($key, $value) {
                return "{$key}={$value}";
            },
            array_keys($config['dsn']),
            $config['dsn']
        );

        $dsn = "msyql:" . join($config, ";");

        $this->pdo = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            $config['options'] ?? []
        );
    }

    /**
     * Gives the user an instance of a MySQLConnection. This ensures that for
     * each user of the site, there is only one MySQLConnection.
     *
     * @return MySQLConnection
     */
    public function getInstance(): MySQLConnection
    {
        if (!$this->connection)
            $this->connection = new MySQLConnection($config['database']);

        return $this->connection;
    }

    /**
     * Execute the given query and return a PDOStatement on success. If there
     * is a failure, null is returned instead.
     *
     * @param  string $query
     *
     * @return PDOStatement|null
     */
    public function execute($query)
    {
        $statement = $this->pdo->prepare($query);

        if (!$statement) return null;

        $success = $statement->execute();

        if (!$success) return null;

        return $statement;
    }
}
