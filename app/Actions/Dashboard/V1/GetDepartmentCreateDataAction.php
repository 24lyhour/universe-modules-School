<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\School;

class GetDepartmentCreateDataAction
{
    public function execute(): array
    {
        $schools = School::where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn ($school) => [
                'value' => (string) $school->id,
                'label' => $school->name . ($school->code ? " ({$school->code})" : ''),
            ]);

        return [
            'schools' => $schools,
        ];
    }
}
