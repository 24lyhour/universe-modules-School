<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\DepartmentResource;
use Modules\School\Models\Department;
use Modules\School\Models\School;

class GetDepartmentEditDataAction
{
    public function execute(Department $department): array
    {
        $department->load('school');

        $schools = School::where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn ($school) => [
                'value' => (string) $school->id,
                'label' => $school->name . ($school->code ? " ({$school->code})" : ''),
            ]);

        return [
            'department' => (new DepartmentResource($department))->resolve(),
            'schools' => $schools,
        ];
    }
}
