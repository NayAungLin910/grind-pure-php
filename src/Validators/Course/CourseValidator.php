<?php

namespace Src\Validators\Course;

use Src\Validators\FormValidator;

class CourseValidator extends FormValidator
{
    public function titleValidate(string $title, string $errorKey): CourseValidator
    {
        $this->checkStringExists($title, $errorKey);

        $this->checkStringWithinDefinedLength($title, $errorKey, 5, 130);

        return $this;
    }

    public function descriptionValidate(string $description, string $errorKey): CourseValidator
    {
        $this->checkStringExists($description, $errorKey);

        return $this;
    }

    public function imageValidate(array $image, string $errorKey, int $index = 0): CourseValidator
    {
        $commonImageExtensions = ['image/jpeg', 'image/png', 'image/gif'];

        $fileExtension = is_array($image['type']) ? $image['type'][$index] : $image['type'];
        $fileSize = is_array($image['size']) ? $image['size'][$index] : $image['size'];

        $this->checkFileExtension($fileExtension, $errorKey, $commonImageExtensions);
        $this->checkFileSize($fileSize, $errorKey, 25);

        return $this;
    }
}
