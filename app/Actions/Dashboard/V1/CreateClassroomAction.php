<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Str;
use Modules\School\Models\Classroom;

class CreateClassroomAction
{
    public function execute(array $data): Classroom
    {
        $data['uuid'] = (string) Str::uuid();

        return Classroom::create($data);
    }
}
