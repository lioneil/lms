<?php

namespace Core\Repositories;

use Core\Application\Repository\Repository;
use Core\Exceptions\RestrictedResourceException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class StorageRepository extends Repository implements Contracts\StorageRepositoryInterface
{
    /**
     * The storage path in local disk.
     *
     * @var string
     */
    protected $storagePath;

    /**
     * Constructor to bind model to a repository.
     *
     * @param string $storagePath
     */
    public function __construct($storagePath = null)
    {
        $this->storagePath = $storagePath ?? storage_path();
    }

    /**
     * Retrieve the storage path.
     *
     * @param  string $path
     * @return string
     */
    protected function storage($path = '')
    {
        return $this->storagePath.DIRECTORY_SEPARATOR.urldecode($path);
    }

    /**
     * Retrieve the file from storage and
     * return the url string.
     *
     * @param  string $file
     * @return string
     */
    public function fetch($file = null)
    {
        $path = $this->storage($file);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        /**
         * Check if the file is allowed to be downloaded.
         * If not, throw the RestrictedResourceException
         *
         * @see \Core\Exceptions\RestrictedResourceException
         */
        if (in_array($extension, config('downloads.restricted', []))) {
            throw new RestrictedResourceException('The requested resource is restricted.');
        }

        if (file_exists($path)) {
            $contentType = config('downloads.mimetypes.'.$extension, 'txt');

            return response()->file($path, ['Content-Type' => $contentType]);
        }

        return abort(404);
    }
}
