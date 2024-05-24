<?php

namespace Src;

use Exception;

class DbConnection
{
    /**
     * Get MySQL Connection
     */
    public static function getMySQLConnection(): object
    {
        $con = mysqli_connect($_ENV['DB_SERVER'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

        if (mysqli_connect_errno()) { // if error code of the last error when connecting the database exists
            throw new Exception(mysqli_connect_error()); // shows the error message of last error
        }

        return $con;
    }
}
