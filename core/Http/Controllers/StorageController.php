<?php

namespace Core\Http\Controllers;

use Core\Application\Repository\WithRepository;
use Core\Repositories\Contracts\StorageRepositoryInterface;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    use WithRepository;

    /**
     * Initialize the repository instance.
     *
     * @param \Core\Repositories\Contracts\StorageRepositoryInterface $repository
     */
    public function __construct(StorageRepositoryInterface $repository)
    {
        $this->repository = $repository;

        parent::__construct();
    }

    /**
     * Retrieve the file from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $file
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request, $file = '')
    {
        return $this->repository()->fetch($file);
    }

    /**
     * Retrieve the file from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $file
     * @return \Illuminate\Http\Response
     */
    public function vendor(Request $request, $file = '')
    {
        $repository = new StorageRepository(base_path('vendor'));

        return $repository->fetch($file);
    }
}
