<?php

class ArrayHelpers
{

    /**
     * Recursive function that checks if the given key
     * of the nested array has the same value
     */
    public static function checkValueSameNestedArray(array $multiDimArr, string $value, string $key): bool
    {
        foreach ($multiDimArr as $row) {
            // if the key value is the same with the value being checked
            if ($row[$key] == $value) {
                return true;
            }
        }

        return false;
    }
}
