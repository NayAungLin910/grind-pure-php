<?php

namespace Src\Validators;

/**
 * Form validation helper provider
 */
class FormValidator
{

    public function __construct(protected array $errors = [])
    {
    }

    /**
     * Get the errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if string is not an empty string
     */
    protected function checkStringExists(string $value, string $errorKey): void
    {
        if ($value == "") {
            $this->errors[$errorKey][] = "Please fill in $errorKey.";
        }
    }

    /**
     * Check if the value is of type string
     */
    protected function checkIfString(mixed $value, string $errorKey): void
    {
        if (!is_string($value)) {
            $this->errors[$errorKey][] = "$errorKey must be string.";
        }
    }

    /**
     * Check if there is whitespace in string
     */
    protected function checkWhiteSpaceExits(string $value, string $errorKey): void
    {
        if (preg_match('/\s/', $value)) {
            $this->errors[$errorKey][] = ["$errorKey must not conatin whisepace."];
        }
    }

    /**
     * Check if string is within the min amd max length
     */
    protected function checkStringWithinDefinedLength(string $value, string $errorKey, int $min, int $max): void
    {
        if (strlen($value) < $min || strlen($value) > $max) {
            $this->errors[$errorKey][] = ["$errorKey must not be less than $min and more than $max."];
        }
    }

    /**
     * Check if string is in email format
     */
    protected function checkEmailFormat(string $value, string $errorKey): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$errorKey][] = ["$errorKey is not a valid email address."];
        }
    }

    /**
     * Check if string has fewer length than min
     */
    protected function checkSringShorterMinLength(string $value, string $errorKey, int $min): void
    {
        if (strlen($value) < $min) {
            $this->errors[$errorKey] = ["$errorKey must be more than $min words."];
        }
    }

    /**
     * Check if string contains at least one digit, 
     * one capital, one special character
     */
    protected function checkStringContainsDigSpecialCap(string $value, string $errorKey): void
    {
        $containsOneDigit = preg_match('/\d/', $value);
        $containsOneCapital = preg_match('/[A-Z]/', $value);
        $containsOneSpecialChar = preg_match('/[^a-zA-Z\d]/', $value);

        if (!$containsOneDigit || !$containsOneCapital || !$containsOneSpecialChar) {
            $this->errors[$errorKey][] = ["$errorKey must contain at least one digit, one captital letter and one special character"];
        }
    }

    /**
     * Check if a model with a given parameter and value exists
     */
    protected function checkPreExistsModel(string|float|int $value, string $errorKey, string $column, string $model): void
    {
        $lastSlashIndex = strrpos($model, "\\");
        $className = substr($model, $lastSlashIndex + 1);

        $model = $model::selectAll()->where($column, $value)->getSingle();

        if (!empty($model)) {
            $this->errors[$errorKey][] = [$className . " with the $column, " . '"' . $value . '"' . " already exists"];
        }
    }

    /**
     * Show errors using sesssions and redirect to previous page
     */
    public function flashErrors(string $redirect = ''): void
    {
        $errors = $this->getErrors();

        if (count($errors) > 0) { // if error(s) exist

            session_start();

            $_SESSION["errors"] = $errors; // set error session

            $redirect = $redirect !== '' ? $redirect : $_SERVER["HTTP_REFERER"];

            header("Location: " . $redirect); // redirects back 
            die();
        }
    }
}
