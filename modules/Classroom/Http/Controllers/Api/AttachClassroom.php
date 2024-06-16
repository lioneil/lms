<?php

namespace Classroom\Http\Controllers\Api;

use Classroom\Http\Requests\AttachClassroomRequest;
use Classroom\Http\Resources\ClassroomResource;
use Classroom\Models\Classroom;
use Classroom\Services\ClassroomServiceInterface;
use Core\Http\Controllers\Api\ApiController;

class AttachClassroom extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Classroom\Http\Requests\AttachClassroomRequest $request
     * @param  \Classroom\Services\ClassroomServiceInterface   $service
     * @param  \Classroom\Models\Classroom                     $classroom
     * @return \Illuminate\Http\Response
     */
    public function __invoke(AttachClassroomRequest $request, ClassroomServiceInterface $service, Classroom $classroom)
    {
        return new ClassroomResource($service->attach($classroom, $request->all()));
    }
}
