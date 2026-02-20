<?php

namespace Modules\School\Http\Requests\Dashboard\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['required', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('school_departments', 'code')->ignore($this->route('department'))],
            'description' => ['nullable', 'string', 'max:2000'],
            'head_of_department' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'office_location' => ['nullable', 'string', 'max:255'],
            'established_year' => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
            'total_staff' => ['nullable', 'integer', 'min:0'],
            'total_students' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'school_id.required' => 'Please select a school.',
            'school_id.exists' => 'The selected school does not exist.',
            'name.required' => 'Department name is required.',
            'name.max' => 'Department name must be less than 255 characters.',
            'code.unique' => 'This department code is already in use.',
            'email.email' => 'Please enter a valid email address.',
            'established_year.min' => 'Established year must be at least 1800.',
            'established_year.max' => 'Established year cannot be in the future.',
        ];
    }
}
