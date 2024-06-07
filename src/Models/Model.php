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

    public static $relationships = []; // relationships with other models following the keywords of laravel eloquent model

    public static array $loadedRelationships = [];

    public function __construct(
        public string $created_at = "",
        public string|null $updated_at = ""
    ) {
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
     * Left join query using the given relationship name of the model
     */
    public static function with(string $relationNameGiven): Model
    {
        if ($relationNameGiven == "") throw new Exception("Please, insert the relationship name!");
        if (count(static::$relationships) == 0) throw new Exception("No relationships for the model is defined");

        foreach (static::$relationships as $relationType => $relationName) { // loop relationships defined for the model
            foreach (static::$relationships[$relationType] as $relationName => $relationInfo) { // loop through the relationship types

                $relationNameSame = $relationNameGiven == $relationName;

                if (($relationType == "belongsTo" || $relationType == "hasMany") && $relationNameSame) { // if one-to-many relationship type
                    static::oneToManyQueryConcat($relationType, $relationName, $relationInfo);
                }

                if ($relationType == "belongsToMany" && $relationNameSame) { //if many-to-many relationship type
                    static::manyToManyQueryConcat($relationType, $relationName, $relationInfo);
                }
            }
        }

        return new static;
    }

    /**
     * One-to-many relationship query string concat
     */
    public static function oneToManyQueryConcat(string $relationType, string $relationName, array $relationInfo): Model
    {
        $relationTableName = $relationInfo["table"];
        $relationForeignColumn = $relationInfo["foreign_id"];
        $primaryColumn = $relationInfo["primary_id"];

        static::$loadedRelationships[$relationName] = $relationInfo; // add realtion info array to the laoded relationships of the model

        static::$query .= " LEFT JOIN " . $relationTableName . " ON " . static::$table . ".$primaryColumn = $relationTableName.$relationForeignColumn";

        return new static;
    }

    /**
     * Many-to-many relationship query string concat
     */
    public static function manyToManyQueryConcat(string $relationType, string $relationName, array $relationInfo): Model
    {
        $pivotTable = $relationInfo["pivot_table"];
        $primaryKey = $relationInfo["primary_key"];
        $foreignKey = $relationInfo["foreign_key"];
        $otherTable = $relationInfo["other_table"];
        $otherTableForeignKey = $relationInfo["other_table_foreign_key"];
        $otherTablePrimaryKey = $relationInfo["other_table_primary_key"];

        static::$loadedRelationships[$relationName] = $relationInfo;

        static::$query .= " LEFT JOIN $pivotTable ON " . static::$table . ".$primaryKey = $pivotTable.$foreignKey";
        static::$query .= " LEFT JOIN $otherTable ON $pivotTable.$otherTableForeignKey = $otherTable.$otherTablePrimaryKey";

        return new static;
    }

    /**
     * Get single row as a class
     */
    public static function getSingle(): Model|null
    {
        static::runQuery(); // run the initialized query

        $model = null;
        $id = "id";

        while ($row = static::$result->fetch_assoc()) {  // fetch row(s) as an associative array

            $model = new static;

            if (isset($model->$id) && $model->$id == $row["id"]) {
                $model = static::iniRelationModels($row, $model);
                continue;
            }

            $model = static::iniModelUsingAssocArray($row);
            $model = static::iniRelationModels($row, $model);
        };

        static::resetStaticValues();

        return $model;
    }

    /**
     * Get single row as an auth model class
     */
    public static function getAuth(): Model|null
    {
        static::runQuery(); // run the initialized query

        $row = static::$result->fetch_assoc(); // fetch row as an associative array

        if ($row === null) return $row;

        static::resetStaticValues();

        return static::iniAuthModel($row);
    }

    /**
     * Get multiple rows as models
     */
    public static function getMultiple(): array
    {
        static::runQuery(); // run the initialized query

        $models = [];
        $index = 0;

        while ($row = static::$result->fetch_assoc()) {

            if (isset($models[$index - 1]->id) && $models[$index - 1]->id == $row["id"]) { // if the model with the same id already exists in array

                $models = static::iniRelationModels($row, $models, $index - 1);
                continue;
            }

            $models[] =  static::iniModelUsingAssocArray($row);
            $models = static::iniRelationModels($row, $models, $index);

            $index++;
        }

        static::resetStaticValues();

        return $models;
    }

    /**
     * Run prepare query
     */
    protected static function runQuery(): void
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

        $stmt->close();
        $dbCon->close();
    }


    /**
     * Initialize relationship models to each of the related model 
     */
    private static function iniRelationModels(array $row, array|Model $models, int $index = 0): array|Model
    {
        if (count(static::$loadedRelationships) > 0) { // if there is(are) loaded relationships of the model
            foreach (static::$loadedRelationships as $relationName => $relationInfo) { // loop the relationship

                $relationModel = new $relationInfo["class"]; // initialize relation model class
                $modelPropertySetCount = 0;

                foreach ($row as $column => $value) {
                    if (str_contains($column, $relationName) && $value !== null) { // if the column is of the relation model

                        $modelProperty = substr($column, strpos($column, "_") + 1); // get the property to set from the column E.g get "title" from "courses_title"
                        $relationModel->$modelProperty =  $value;
                        $modelPropertySetCount++;
                    }
                }

                // if no property of the relation model class is set, returns the existing models
                if ($modelPropertySetCount == 0) return $models;

                $modelsIsArray = is_array($models);
                $idExists = false;

                if ($modelsIsArray) { // check if a relation model with the same id already exists
                    foreach ($models[$index]->$relationName as $i => $rModel) {
                        if ($rModel->id === $relationModel->id) {
                            $idExists = true;
                        }
                    }
                }

                if ($modelsIsArray && !$idExists) {
                    $models[$index]->$relationName[] = $relationModel; // append the relation model class to the relation model array property of the model
                } elseif (!$idExists) {
                    $models->$relationName[] = $relationModel;
                }
            }
        }
        return $models;
    }

    /**
     * Initialize model class through associative array
     */
    private static function iniModelUsingAssocArray(array $row): Model
    {

        $selfClass = new static;

        foreach ($row as $columnName => $value) {

            // if columnName is password or the property with the same columnName does not exist, skip
            if ($columnName === 'password' || !isset($selfClass->$columnName)) continue;

            $selfClass->$columnName = $value;
        }

        return $selfClass;
    }

    /**
     * Initialize auth model class for authentication
     */
    private static function iniAuthModel(array $row): Model
    {

        $selfClass = new static;

        foreach ($row as $columnName => $value) {
            $selfClass->$columnName = $value;
        }

        return $selfClass;
    }

    /**
     * Rest the static values
     */
    protected static function resetStaticValues(): void
    {
        static::$query = '';
        static::$loadedRelationships = [];
        static::$values = [];
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
}
