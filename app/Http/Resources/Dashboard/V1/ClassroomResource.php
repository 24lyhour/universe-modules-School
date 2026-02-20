<?php

namespace Modules\School\Http\Resources\Dashboard\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\School\Models\Classroom;

class ClassroomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'department_id' => $this->department_id,
            'name' => $this->name,
            'code' => $this->code,
            'building' => $this->building,
            'floor' => $this->floor,
            'capacity' => $this->capacity,
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'equipment' => $this->equipment ?? [],
            'description' => $this->description,
            'has_projector' => $this->has_projector,
            'has_whiteboard' => $this->has_whiteboard,
            'has_ac' => $this->has_ac,
            'is_available' => $this->is_available,
            'status' => $this->status,
            'full_location' => $this->full_location,
            'department_name' => $this->whenLoaded('department', fn () => $this->department?->name),
            'school_name' => $this->whenLoaded('department', fn () => $this->department?->school?->name),
            'department' => $this->whenLoaded('department', fn () => [
                'id' => $this->department->id,
                'name' => $this->department->name,
                'school_id' => $this->department->school_id,
                'school' => $this->department->school ? [
                    'id' => $this->department->school->id,
                    'name' => $this->department->school->name,
                ] : null,
            ]),
            'courses_count' => $this->whenCounted('courses'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    protected function getTypeLabel(): string
    {
        $types = Classroom::getClassroomTypes();
        return $types[$this->type] ?? ucfirst($this->type);
    }
}
