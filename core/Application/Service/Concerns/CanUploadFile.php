<?php

namespace Core\Application\Service\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait CanUploadFile
{
    /**
     * The pathname of the uploaded file.
     *
     * @var string
     */
    protected $pathname;

    /**
     * Upload the given file.
     *
     * @param  \Illuminate\Http\UploadedFile $file
     * @param  string                        $folder
     * @return string|null
     */
    public function upload(UploadedFile $file, $folder = null)
    {
        $folderName = settings(
            'storage:modules', 'modules/'.$this->getTable()
        ).DIRECTORY_SEPARATOR.date('Y-m-d');
        $folderName = $folderName.($folder ? DIRECTORY_SEPARATOR.$folder : $folder);
        $uploadPath = storage_path($folderName);
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fileName = $name.'-'.date('mdYHis').'.'.$file->getClientOriginalExtension();
        $this->pathname = $uploadPath.DIRECTORY_SEPARATOR.$fileName;

        if ($file->move($uploadPath, $fileName)) {
            $url = str_replace(DIRECTORY_SEPARATOR, '/', "storage/$folderName/$fileName");
            $url = str_replace('//', '/', $url);

            return url($url);
        }

        return null;
    }

    /**
     * Retrieve the upload file's path.
     *
     * @return string
     */
    public function getPathname()
    {
        return $this->pathname ?? null;
    }

    /**
     * Delete the file from storage if exists.
     *
     * @param  string $pathname
     * @return void
     */
    public function deleteFileFromStorage($pathname): void
    {
        if (file_exists($pathname)) {
            File::delete($pathname);
        }
    }
}
