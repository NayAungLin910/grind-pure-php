<?php

namespace Src;

use Exception;

class DbConnection
{
    const DB_SERVER = "localhost";
    const DB_USERNAME = "root";
    const DB_PASSWORD = "password";
    const DB_NAME = "grind_database";

    public $con;

    /**
     * Get MySQL Connection
     */
    public function getMySQLConnection(): object
    {
        $con = mysqli_connect(self::DB_SERVER, self::DB_USERNAME, self::DB_PASSWORD, self::DB_NAME);

        if ($con === false) {
            throw new Exception("Database connection failed due to " .  mysqli_connect_error());
        }

        return $con;
    }
}
