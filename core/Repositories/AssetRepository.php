<?php

namespace Core\Repositories;

use Core\Application\Repository\Repository;
use Core\Exceptions\RestrictedResourceException;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class AssetRepository extends Repository implements Contracts\AssetRepositoryInterface
{
    /**
     * The base path in local disk.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Constructor to bind model to a repository.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \Illuminate\Http\Request $user
     */
    public function __construct()
    {
        $this->basePath = config('app.debug') ? base_path() : resource_path();
    }

    /**
     * Retrieve the base path.
     *
     * @param string $path
     * @return string
     */
    protected function base($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.urldecode($path);
    }

    /**
     * Retrieve the file from base and
     * return the url string.
     *
     * @param string $file
     * @return string
     */
    public function fetch($file = null)
    {
        $path = $this->base($file);
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

            return response()->file($path, array('Content-Type' => $contentType));
        }

        return abort(404);
    }
}
