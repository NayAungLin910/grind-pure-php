<?php

namespace Src\Validators\Answer;

use Src\Validators\FormValidator;

class AnswerValidator extends FormValidator
{
    /**
     * Handles description validate
     */
    public function descriptionValidate(string $description, string $errorKey): AnswerValidator
    {
        $this->checkStringExists($description, $errorKey);

        return $this;
    }

    /**
     * Handles explanation validate
     */
    public function explanationValidate(string $explanation, string $errorKey): AnswerValidator
    {
        $this->checkStringExists($explanation, $errorKey);

        return $this;
    }
}
