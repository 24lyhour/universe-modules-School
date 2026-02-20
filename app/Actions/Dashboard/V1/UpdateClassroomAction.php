<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Classroom;

class UpdateClassroomAction
{
    public function execute(Classroom $classroom, array $data): Classroom
    {
        $classroom->update($data);

        return $classroom->fresh();
    }
}
