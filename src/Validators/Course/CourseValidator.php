<?php

namespace Src\Validators\Course;

use Src\Models\Course;
use Src\Validators\FormValidator;

class CourseValidator extends FormValidator
{
    public function titleValidate(string $title, string $errorKey): CourseValidator
    {
        $this->checkStringExists($title, $errorKey);
        $this->checkStringWithinDefinedLength($title, $errorKey, 5, 130);
        $this->checkPreExistsModel($title, $errorKey, 'title', Course::class);

        return $this;
    }

    public function titleEditValidate(string $title, string $errorKey, int $course_id): CourseValidator
    {
        $this->checkStringExists($title, $errorKey);
        $this->checkStringWithinDefinedLength($title, $errorKey, 5, 130);
        $this->checkPreExistsModel($title, $errorKey, 'title', Course::class, $course_id);

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

    public function tagValidate(array|null $tags, string $errorKey): CourseValidator
    {
        $this->checkArrayNotEmpty($tags, 'tags');

        return $this;
    }

    public function titleSingleCourseValidate(string $title, string $errorKey): CourseValidator
    {
        $this->checkStringExists($title, $errorKey);
        $this->checkStringWithinDefinedLength($title, $errorKey, 5, 130);

        return $this;
    }
}
