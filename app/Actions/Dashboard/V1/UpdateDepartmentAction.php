<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Department;

class UpdateDepartmentAction
{
    public function execute(Department $department, array $data): Department
    {
        $department->update($data);

        return $department->fresh();
    }
}
