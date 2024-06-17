<?php

namespace Src\Validators\Tag;

use Src\Models\Tag;
use Src\Validators\FormValidator;

class TagValidator extends FormValidator
{
    /**
     * Validates name
     */
    public function nameValidate(string $name, string $errorKey, int $exceptId = 0): TagValidator
    {
        $this->checkStringExists($name, $errorKey);
        $this->checkPreExistsModel($name, $errorKey, 'name', Tag::class, $exceptId);

        return $this;
    }
}
