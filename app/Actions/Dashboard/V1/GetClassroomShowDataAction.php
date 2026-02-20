<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\ClassroomResource;
use Modules\School\Models\Classroom;

class GetClassroomShowDataAction
{
    public function execute(Classroom $classroom): array
    {
        $classroom->load(['school', 'courses']);
        $classroom->loadCount(['courses']);

        return [
            'classroom' => (new ClassroomResource($classroom))->resolve(),
            'stats' => [
                'courses_count' => $classroom->courses_count,
                'capacity' => $classroom->capacity,
            ],
        ];
    }
}
