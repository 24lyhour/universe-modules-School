<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\School\Actions\Dashboard\V1\CreateDepartmentAction;
use Modules\School\Actions\Dashboard\V1\DeleteDepartmentAction;
use Modules\School\Actions\Dashboard\V1\GetDepartmentCreateDataAction;
use Modules\School\Actions\Dashboard\V1\GetDepartmentEditDataAction;
use Modules\School\Actions\Dashboard\V1\GetDepartmentIndexDataAction;
use Modules\School\Actions\Dashboard\V1\GetDepartmentShowDataAction;
use Modules\School\Actions\Dashboard\V1\ToggleDepartmentStatusAction;
use Modules\School\Actions\Dashboard\V1\UpdateDepartmentAction;
use Modules\School\Http\Requests\Dashboard\V1\StoreDepartmentRequest;
use Modules\School\Http\Requests\Dashboard\V1\UpdateDepartmentRequest;
use Modules\School\Http\Resources\Dashboard\V1\DepartmentResource;
use Modules\School\Models\Department;

class DepartmentController extends Controller
{
    public function __construct(
        protected GetDepartmentIndexDataAction $getIndexDataAction,
        protected GetDepartmentShowDataAction $getShowDataAction,
        protected GetDepartmentCreateDataAction $getCreateDataAction,
        protected GetDepartmentEditDataAction $getEditDataAction,
        protected CreateDepartmentAction $createAction,
        protected UpdateDepartmentAction $updateAction,
        protected DeleteDepartmentAction $deleteAction,
        protected ToggleDepartmentStatusAction $toggleStatusAction,
    ) {}

    /**
     * Display a listing of departments.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'status', 'school_id']);

        $data = $this->getIndexDataAction->execute($perPage, $filters);

        return Inertia::render('school::Dashboard/V1/Department/Index', $data);
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(): Modal
    {
        $data = $this->getCreateDataAction->execute();

        return Inertia::modal('school::Dashboard/V1/Department/Create', $data)
            ->baseRoute('school.departments.index');
    }

    /**
     * Store a newly created department.
     */
    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $this->createAction->execute($request->validated());

        return redirect()
            ->route('school.departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department): Response
    {
        $data = $this->getShowDataAction->execute($department);

        return Inertia::render('school::Dashboard/V1/Department/Show', $data);
    }

    /**
     * Show the form for editing the department.
     */
    public function edit(Department $department): Modal
    {
        $data = $this->getEditDataAction->execute($department);

        return Inertia::modal('school::Dashboard/V1/Department/Edit', $data)
            ->baseRoute('school.departments.index');
    }

    /**
     * Update the specified department.
     */
    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $this->updateAction->execute($department, $request->validated());

        return redirect()
            ->route('school.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(Department $department): Modal
    {
        $department->loadCount(['programs', 'courses', 'employees']);

        return Inertia::modal('school::Dashboard/V1/Department/Delete', [
            'department' => (new DepartmentResource($department))->resolve(),
        ])->baseRoute('school.departments.index');
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Department $department): RedirectResponse
    {
        $this->deleteAction->execute($department);

        return redirect()
            ->route('school.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * Toggle department status.
     */
    public function toggleStatus(Department $department): RedirectResponse
    {
        $this->toggleStatusAction->execute($department);

        $status = $department->status ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Department {$status} successfully.");
    }

    /**
     * Get departments for API/dropdown.
     */
    public function getDepartments(Request $request): JsonResponse
    {
        $schoolId = $request->input('school_id');

        $query = Department::where('status', true);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $departments = $query->orderBy('name')->get(['id', 'name', 'code', 'school_id']);

        return response()->json([
            'departments' => $departments,
        ]);
    }
}
