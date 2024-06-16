<?php

namespace Core\Http\Controllers\Api\Localization;

use Core\Http\Controllers\Api\ApiController;
use Core\Repositories\LanguageRepository;
use Illuminate\Http\Request;

class GetSupportedLanguages extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Core\Repositories\LanguageRepository $repository
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LanguageRepository $repository)
    {
        return response()->json($repository->supported());
    }
}
