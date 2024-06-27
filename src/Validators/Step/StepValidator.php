<?php

namespace Src\Validators\Step;

use Src\Validators\FormValidator;

class StepValidator extends FormValidator
{
    public function titleValidate(string $title, string $errorKey): StepValidator
    {
        $this->checkStringExists($title, $errorKey);
        $this->checkStringWithinDefinedLength($title, $errorKey, 5, 130);

        return $this;
    }

    public function descriptionValidate(string $description, string $errorKey): StepValidator
    {
        $this->checkStringExists($description, $errorKey);
        return $this;
    }

    public function readingContextValidate(string $readingContext, string $errorKey): StepValidator
    {
        $this->checkStringExists($readingContext, $errorKey);
        return $this;
    }

    public function videoValidate(array $video, string $errorKey): StepValidator
    {
        $commonVideoExtensions = ['video/mp4', 'video/avi', 'video/mpeg', 'video/quicktime', 'video/x-ms-wmv'];

        $fileExtension = $video['type'];
        $fileSize = $video['size'];

        $this->checkFileExtension($fileExtension, $errorKey, $commonVideoExtensions);
        $this->checkFileSize($fileSize, $errorKey, 25);

        return $this;
    }

    public function priorityValidate(string $priority, string $errorKey): StepValidator
    {
        $this->checkInteger($priority, $errorKey);

        return $this;
    }
}
