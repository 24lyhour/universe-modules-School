<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\ClassroomResource;
use Modules\School\Models\Classroom;
use Modules\School\Models\Department;

class GetClassroomEditDataAction
{
    public function execute(Classroom $classroom): array
    {
        $departments = Department::with('school')
            ->where('status', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($department) => [
                'id' => $department->id,
                'name' => $department->school
                    ? "{$department->name} ({$department->school->name})"
                    : $department->name,
            ])
            ->toArray();

        return [
            'classroom' => (new ClassroomResource($classroom))->resolve(),
            'types' => Classroom::getClassroomTypes(),
            'departments' => $departments,
            'equipmentOptions' => $this->getEquipmentOptions(),
        ];
    }

    protected function getEquipmentOptions(): array
    {
        return [
            ['value' => 'projector', 'label' => 'Projector'],
            ['value' => 'whiteboard', 'label' => 'Whiteboard'],
            ['value' => 'smartboard', 'label' => 'Smart Board'],
            ['value' => 'computer', 'label' => 'Computer'],
            ['value' => 'audio_system', 'label' => 'Audio System'],
            ['value' => 'video_conferencing', 'label' => 'Video Conferencing'],
            ['value' => 'document_camera', 'label' => 'Document Camera'],
            ['value' => 'lab_equipment', 'label' => 'Lab Equipment'],
            ['value' => 'workshop_tools', 'label' => 'Workshop Tools'],
        ];
    }
}
