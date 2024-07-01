<?php

namespace Src\Validators\Question;

use Src\Validators\FormValidator;

class QuestionValidator extends FormValidator
{
    /**
     * Validates description
     */
    public function descriptionValidate(string $description, string $errorKey): QuestionValidator
    {
        $this->checkStringExists($description, $errorKey);

        return $this;
    }

    /**
     * Step Id validates
     */
    public function stepIdValidate(mixed $stepId, string $errorKey): QuestionValidator
    {
        $this->checkInteger($stepId, 'step-id');

        return $this;
    }
}
