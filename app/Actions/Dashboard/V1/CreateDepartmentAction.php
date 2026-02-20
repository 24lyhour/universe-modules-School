<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Str;
use Modules\School\Models\Department;

class CreateDepartmentAction
{
    public function execute(array $data): Department
    {
        $data['uuid'] = (string) Str::uuid();

        return Department::create($data);
    }
}
