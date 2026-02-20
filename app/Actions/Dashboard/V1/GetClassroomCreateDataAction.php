<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Classroom;
use Modules\School\Models\Department;

class GetClassroomCreateDataAction
{
    public function execute(): array
    {
        $departments = Department::where('status', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($department) => [
                'id' => $department->id,
                'name' => $department->name,
            ])
            ->toArray();

        return [
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
