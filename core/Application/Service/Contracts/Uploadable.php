<?php

namespace Core\Application\Service\Contracts;

use Illuminate\Http\UploadedFile;

interface Uploadable
{
    /**
     * Upload the given file.
     *
     * @param  \Illuminate\Http\UploadedFile $file
     * @return string|null
     */
    public function upload(UploadedFile $file);
}
