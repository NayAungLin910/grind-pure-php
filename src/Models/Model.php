<?php

namespace Src\Models;

use Exception;
use Src\DbConnection;
use Src\Models\Enums\ModelSQLEnum;

class Model
{

    public static $table = '';

    public static $query = "";

    public static object|null|false $result = null;

    public static array $values = [];

    public function __construct()
    {
    }

    /**
     * Insert a new row of the model using given associative 
     * array
     */
    public static function create(array $values): void
    {
        static::$query = "INSERT INTO " . static::$table;

        static::$values = array_merge(static::$values, $values);

        $legnthOfValuesArray = count($values);
        $count = 0;

        if ($legnthOfValuesArray == 0) {
            throw new Exception("The values must not be an empty array!");
        }

        foreach ($values as $key => $value) { // concat query with columns

            if ($legnthOfValuesArray == 1) { // if only one pair
                static::$query .= " ($key) ";
            } elseif ($count == 0) { // if the value is the first one
                static::$query .= " ($key,";
            } elseif ($count == $legnthOfValuesArray - 1) { // if the value is the last one
                static::$query .= " $key)";
            } else {
                static::$query .= " $key,";
            }

            $count++;
        }

        static::$query .= " VALUES";

        $count = 0;

        foreach ($values as $key => $value) { // concat values 

            if ($legnthOfValuesArray == 1) { // if only one pair
                static::$query .= " (?) ";
            } elseif ($count == 0) { // if the value is the first one
                static::$query .= " (?,";
            } elseif ($count == $legnthOfValuesArray - 1) { // if the value is the last one
                static::$query .= " ?)";
            } else {
                static::$query .= " ?,";
            }

            $count++;
        }

        static::runQuery(); // run query
    }

    /**
     * Select columns from table
     */
    public static function select(array $columns): static
    {

        static::$query .= "SELECT";

        $legnthOfValuesArray = count($columns);
        $count = 0;

        foreach ($columns as $key => $value) { // concat values 

            if ($legnthOfValuesArray == 1) { // if only one column
                static::$query .= " $value ";
            } elseif ($count == 0) { // if the value is the first column
                static::$query .= " $value,";
            } elseif ($count == $legnthOfValuesArray - 1) { // if the value is the last column
                static::$query .= " $value";
            } else {
                static::$query .= " $value,";
            }

            $count++;
        }

        static::$query .= " FROM " . static::$table;

        return new static;
    }

    /**
     * Select all columns
     */
    public static function selectAll(): Model
    {
        static::$query .= "SELECT * FROM " . static::$table;

        return new static;
    }

    /**
     * Get latest record
     */
    public static function orderBy(string $column, ModelSQLEnum $sortingOrder): Model
    {
        static::$query .= " ORDER BY $column $sortingOrder->value";

        return new static;
    }

    /**
     * Where clause query
     */
    public static function where(string $column, string|int|float $value)
    {
        static::$query .= " WHERE $column = ?";

        static::$values[] = $value;

        return new static;
    }

    /**
     * And where clause concat
     */
    public static function andWhere(string $column, string|int|float $value)
    {
        static::$query .= " AND $column = ?";

        static::$values[] = $value;

        return new static;
    }

    /**
     * Or where clause concat
     */
    public static function orWhere(string $column, string|int|float $value)
    {
        static::$query .= " OR $column = ?";

        static::$values[] = $value;

        return new static;
    }

    /**
     * Takes rows only up to given number of rows
     */
    public static function limit(int $numOfRows): Model
    {
        static::$query .= " LIMIT ?";

        static::$values[] = $numOfRows;

        return new static;
    }

    /**
     * Offset rows up to given number of rows
     */
    public static function offset(int $numOfRows): Model
    {
        static::$query .= " OFFSET ?";

        static::$values[] = $numOfRows;

        return new static;
    }


    /**
     * Get single row as a class
     */
    public static function getSingle(): Model|null
    {
        static::runQuery(); // run the initialized query

        $row = static::$result->fetch_assoc(); // fetch row as an associative array

        if ($row === null) return $row;

        return static::iniModelUsingAssocArray($row);
    }

    /**
     * Run prepare query
     */
    private static function runQuery(): void
    {
        $dbCon = DbConnection::getMySQLConnection(); // get database connection

        $stmt = $dbCon->prepare(static::$query); // prepare statement

        $types = "";

        if (count(static::$values) > 0) {

            foreach (static::$values as $key => $value) {
                $types .= static::getBindParamTypeString($value);
            }

            $stmt->bind_param($types, ...array_values(static::$values)); // bind parameters to prepared sql query


        }

        $stmt->execute();

        $result = $stmt->get_result();


        static::$result = $result;

        static::$query = "";
        static::$values = [];

        $stmt->close();
        $dbCon->close();
    }

    /**
     * Get bind param type string
     */
    private static function getBindParamTypeString(string|int|float $value): string
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
     * Initialize model class through associative array
     */
    private static function iniModelUsingAssocArray(array $row): Model
    {
        $selfClass = new static;

        foreach ($row as $columnName => $value) {
            if ($columnName === 'password') continue; // if password column detected, skips loop
            $selfClass->$columnName = $value;
        }

        return $selfClass;
    }
}
