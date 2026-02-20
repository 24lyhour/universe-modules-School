<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\DepartmentResource;
use Modules\School\Http\Resources\Dashboard\V1\InstitutionResource;
use Modules\School\Http\Resources\Dashboard\V1\ProgramResource;
use Modules\School\Models\Institution;

class GetInstitutionShowDataAction
{
    public function execute(Institution $institution): array
    {
        $institution->load(['departments', 'programs', 'employees']);
        $institution->loadCount(['departments', 'programs', 'employees', 'courses']);

        $departments = $institution->departments()
            ->withCount(['programs', 'courses', 'employees'])
            ->orderBy('name')
            ->get();

        $programs = $institution->programs()
            ->with('department')
            ->withCount('courses')
            ->orderBy('name')
            ->get();

        return [
            'institution' => (new InstitutionResource($institution))->resolve(),
            'departments' => DepartmentResource::collection($departments)->resolve(),
            'programs' => ProgramResource::collection($programs)->resolve(),
            'stats' => [
                'departments_count' => $institution->departments_count,
                'programs_count' => $institution->programs_count,
                'employees_count' => $institution->employees_count,
                'courses_count' => $institution->courses_count,
            ],
        ];
    }
}
