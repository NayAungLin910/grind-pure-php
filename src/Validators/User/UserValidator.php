<?php

namespace Src\Validators\User;

use Src\Models\User;
use Src\Validators\FormValidator;

/**
 * Handles validation for user model
 */
class UserValidator extends FormValidator
{

    /**
     * Validates name
     */
    public function nameValidate(string $name, string $errorKey): UserValidator
    {
        $this->checkIfString($name, $errorKey);

        $this->checkStringExists($name, $errorKey);

        $this->checkWhiteSpaceExits($name, $errorKey);

        $this->checkStringWithinDefinedLength($name, $errorKey, 6, 20);

        $this->checkPreExistsModel($name, 'name', 'name', User::class);

        return $this;
    }

    /**
     * Validates email
     */
    public function emailValidate(string $email, string $errorKey): UserValidator
    {
        $this->checkIfString($email, $errorKey);

        $this->checkStringExists($email, $errorKey);

        $this->checkWhiteSpaceExits($email, $errorKey);

        $this->checkEmailFormat($email, $errorKey);

        return $this;
    }

    /**
     * Validates password
     */
    public function passwordValidate(string $password, string $errorKey): UserValidator
    {
        $this->checkIfString($password, $errorKey);

        $this->checkStringExists($password, $errorKey);

        $this->checkStringWithinDefinedLength($password, $errorKey, 8, 25);

        $this->checkStringContainsDigSpecialCap($password, $errorKey);

        return $this;
    }

    /**
     * Login Password validation
     */
    public function loginPasswordValidate(string $password, string $errorKey): UserValidator
    {
        $this->checkIfString($password, $errorKey);

        $this->checkStringExists($password, $errorKey);

        return $this;
    }
}
