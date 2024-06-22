<?php

namespace Src\Validators\Section;

use Src\Validators\FormValidator;

class SectionValidator extends FormValidator
{
    /**
     * Validates title field 
     */
    public function titleValidate(string $title, string $errorKey): SectionValidator
    {
        $this->checkStringExists($title, $errorKey);
        $this->checkStringWithinDefinedLength($title, $errorKey, 5, 130);

        return $this;
    }

    /**
     * Validates description
     */
    public function descriptionValidate(string $description, string $errorKey): SectionValidator
    {
        $this->checkStringExists($description, $errorKey);

        return $this;
    }

    /**
     * Validates priority
     */
    public function priorityValidate(mixed $priority, string $errorKey): SectionValidator
    {
        $this->checkInteger($priority, "priority");

        return $this;
    }
}
