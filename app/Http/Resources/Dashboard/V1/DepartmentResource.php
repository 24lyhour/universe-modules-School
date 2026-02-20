<?php

namespace Modules\School\Http\Resources\Dashboard\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'institution_id' => $this->institution_id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'head_of_department' => $this->head_of_department,
            'email' => $this->email,
            'phone' => $this->phone,
            'office_location' => $this->office_location,
            'established_year' => $this->established_year,
            'total_staff' => $this->total_staff,
            'total_students' => $this->total_students,
            'status' => $this->status,
            'institution_name' => $this->whenLoaded('institution', fn() => $this->institution?->name),
            'programs_count' => $this->whenCounted('programs'),
            'courses_count' => $this->whenCounted('courses'),
            'employees_count' => $this->whenCounted('employees'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
