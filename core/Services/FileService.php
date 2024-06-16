<?php

namespace Core\Services;

use Core\Application\Service\Concerns\CanUploadFile;
use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class FileService extends Service implements FileServiceInterface
{
    use CanUploadFile;

    /**
     * The UploadedFile instance.
     *
     * @var \Illuminate\Http\UploadedFile
     */
    protected $file;

    /**
     * The default folder name to upload files.
     *
     * @var string
     */
    protected $folder = 'files';

    /**
     * Constructor to bind model to a repository.
     *
     * @param  \Illuminate\Http\Request      $request
     * @param  \Illuminate\Http\UploadedFile $file
     * @return void
     */
    public function __construct(Request $request, UploadedFile $file = null)
    {
        $this->request = $request;
        $this->file = $file;
    }

    /**
     * Define the validation rules for the model.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'required|file',
        ];
    }

    /**
     * Retrieve the model's table name.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->request()->input('folder') ?: $this->folder;
    }
}
