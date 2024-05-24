<?php

namespace Src\Models;

use Exception;
use Src\DbConnection;

class Model
{

    protected static $table = '';

    protected static $selectQuery = 'SELECT';

    protected static $result = null;

    public function __construct()
    {
    }

    /**
     * Insert a new row of the model using given associative 
     * array
     */
    public static function create(array $data): void
    {
        $insertQuery = "INSERT INTO " . static::$table;

        $lengthOfData = count($data);
        $count = 0;

        if ($lengthOfData == 0) {
            throw new Exception("The values must not be empty array!");
        }

        foreach ($data as $key => $value) { // concat query with columns

            if ($lengthOfData == 1) { // if only one pair
                $insertQuery .= " ($key) ";
            } elseif ($count == 0) { // if the value is the first one
                $insertQuery .= " ($key,";
            } elseif ($count == $lengthOfData - 1) { // if the value is the last one
                $insertQuery .= " $key)";
            } else {
                $insertQuery .= " $key,";
            }

            $count++;
        }

        $insertQuery .= " VALUES";

        $count = 0;

        $types = ""; // types for bind params

        foreach ($data as $key => $value) { // concat values 

            if ($lengthOfData == 1) { // if only one pair
                $insertQuery .= " (?) ";
            } elseif ($count == 0) { // if the value is the first one
                $insertQuery .= " (?,";
            } elseif ($count == $lengthOfData - 1) { // if the value is the last one
                $insertQuery .= " ?)";
            } else {
                $insertQuery .= " ?,";
            }

            $typeStringParam = static::getBindParamTypeString($value); // 

            $types .= "$typeStringParam";

            $count++;
        }

        $dbCon = DbConnection::getMySQLConnection(); // get database connection

        $stmt = $dbCon->prepare($insertQuery); // prepare statement

        $stmt->bind_param($types, ...array_values($data));

        $stmt->execute(); // execute statement

        $stmt->close();
        $dbCon->close();
    }

    /**
     * Select columns from table
     */
    public static function select(array $columns): static
    {

        $lengthOfData = count($columns);
        $count = 0;

        foreach ($columns as $key => $value) { // concat values 

            if ($lengthOfData == 1) { // if only one column
                static::$selectQuery .= " $value ";
            } elseif ($count == 0) { // if the value is the first column
                static::$selectQuery .= " $value,";
            } elseif ($count == $lengthOfData - 1) { // if the value is the last column
                static::$selectQuery .= " $value";
            } else {
                static::$selectQuery .= " $value,";
            }

            $count++;
        }

        static::$selectQuery .= " FROM " . static::$table;

        return new static;
    }

    /**
     * Find By id
     */
    public static function findById(int $id): Model|null
    {
        static::$selectQuery .= " WHERE id = ?";

        $dbCon = DbConnection::getMySQLConnection(); // get database connection

        $stmt = $dbCon->prepare(static::$selectQuery); // prepare statement

        $stmt->bind_param("i", $id);

        $stmt->execute(); // execute statement

        $result = $stmt->get_result(); // get mysqli result

        $row = $result->fetch_assoc(); // fetch row as an associative array

        $row =  static::handleNullRow($row);

        if ($row === null) return $row;

        $stmt->close();
        $dbCon->close();

        return static::iniModelUsingAssocArray($row);
    }

    /**
     * Get latest record
     */
    public static function getLatestRow(): Model|null
    {
        static::$selectQuery .= " ORDER BY id DESC LIMIT 1";

        $dbCon = DbConnection::getMySQLConnection(); // get database connection

        $result = $dbCon->query(static::$selectQuery);

        $row = $result->fetch_assoc(); // fetch row as an associative array

        $row =  static::handleNullRow($row);

        if ($row === null) return $row;

        return static::iniModelUsingAssocArray($row);
    }

    /**
     * Get bind param type string
     */
    private static function getBindParamTypeString(mixed $value): string
    {
        switch (gettype($value)) {
            case "string":
                return "s";
            case "double":
                return "d";
            case "integer":
                return "i";
        }
    }


    /**
     * Where clause query get value
     */
    private static function where(string $column, string|int|float $value)
    {
        static::$selectQuery .= " WHERE $column = $value";

        // $dbCon = DbConnection::getMySQLConnection(); // get database connection

        // $result = $dbCon->query(static::$selectQuery);

        // $row = $result->fetch_assoc(); // fetch row as an associative array

        // $row =  static::handleNullRow($row);

        // if ($row === null) return $row;
    }

    /**
     * Run normal query
     */
    private static function runNormalQuery(): void
    {
        $dbCon = DbConnection::getMySQLConnection(); // get database connection

        $stmt = $dbCon->prepare(static::$selectQuery); // prepare statement



        $result = $dbCon->query(static::$selectQuery);
    }

    /**
     * Run prepare query
     */
    private static function runPrepareQuery(): void
    {
        $dbCon = DbConnection::getMySQLConnection(); // get database connection


    }

    /**
     * Get single row as a class
     */
    private static function getSingle(): Model|null
    {
        $dbCon = DbConnection::getMySQLConnection(); // get database connection

        $result = $dbCon->query(static::$selectQuery);

        $row = $result->fetch_assoc(); // fetch row as an associative array

        $row =  static::handleNullRow($row);

        if ($row === null) return $row;

        return static::iniModelUsingAssocArray($row);
    }

    /**
     * Handle null row
     */
    private static function handleNullRow(array|null $row): null|array
    {
        if ($row === null || count($row) == 0) {
            return null;
        }

        return $row;
    }

    /**
     * Initialize model class through associative array
     */
    private static function iniModelUsingAssocArray(array $row): Model
    {
        $selfClass = new static;

        foreach ($row as $columnName => $value) {
            if ($columnName === 'passsword') continue; // if password column detected, skips loop
            $selfClass->$columnName = $value;
        }

        return $selfClass;
    }
}
