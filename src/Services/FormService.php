<?php

namespace Src\Services;

use Src\Validators\FormValidator;

class FormService
{
    public function __construct(public $formValidator = new FormValidator())
    {
    }

    /**
     * Upload file(s) from request to the public directory
     */
    public function uploadFiles(array $files, string $directory, string $errorKey): string|bool
    {
        if (!is_array($files['name'])) { // if request is single flie upload
            return $this->singleFileUpload($files, $directory, $errorKey);
        }

        return $this->multipleFilesUpload($files, $directory, $errorKey);
    }

    /**
     * Delete file specified, if it exists
     */
    public function deleteFile(string $file): void
    {
        if (file_exists('.' . $file)) unlink('.' . $file);
    }

    /**
     * Upload single file
     */
    protected function singleFileUpload(array $files, string $directory, string $errorKey): string
    {
        if ($files['error'] !== UPLOAD_ERR_OK) { // if file upload error
            $this->formValidator->addError($errorKey, "$errorKey, file upload failed.");
            $this->formValidator->flashErrors();
        }

        $exploadedArray = explode(".", $files['name']);
        $fileExtension = end($exploadedArray);
        $fileName = uniqid() . "." . $fileExtension; // unique file name
        $directory .= "/$fileName";

        if (move_uploaded_file($files['tmp_name'], "." . $directory) == false) {
            $this->formValidator->addError($errorKey, "$errorKey, file upload failed.");
            $this->formValidator->flashErrors();
        };

        return $directory;
    }

    /**
     * Multiple files upload
     */
    protected function multipleFilesUpload(array $files, string $directory, string $errorKey): bool
    {
        $fileCount = count($files['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) { // if file upload error
                $this->formValidator->addError($errorKey, "$errorKey, file upload failed.");
                $this->formValidator->flashErrors();
            }

            $exploadedArray = explode(".", $files['name'][$i]);
            $fileExtension = end($exploadedArray);
            $fileName = uniqid() . "." . $fileExtension; // unique file name
            $dir = $directory . "/$fileName";

            if (move_uploaded_file($files['tmp_name'][$i], "." . $dir) == false) {
                $this->formValidator->addError($errorKey, "$errorKey, $i file upload failed.");
                $this->formValidator->flashErrors();
            }
        }

        return true;
    }
}
