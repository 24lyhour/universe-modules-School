<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\School\Actions\Dashboard\V1\CreateInstitutionAction;
use Modules\School\Actions\Dashboard\V1\DeleteInstitutionAction;
use Modules\School\Actions\Dashboard\V1\GetInstitutionCreateDataAction;
use Modules\School\Actions\Dashboard\V1\GetInstitutionEditDataAction;
use Modules\School\Actions\Dashboard\V1\GetInstitutionIndexDataAction;
use Modules\School\Actions\Dashboard\V1\GetInstitutionShowDataAction;
use Modules\School\Actions\Dashboard\V1\ToggleInstitutionStatusAction;
use Modules\School\Actions\Dashboard\V1\UpdateInstitutionAction;
use Modules\School\Http\Requests\Dashboard\V1\StoreInstitutionRequest;
use Modules\School\Http\Requests\Dashboard\V1\UpdateInstitutionRequest;
use Modules\School\Http\Resources\Dashboard\V1\InstitutionResource;
use Modules\School\Models\Institution;
use Modules\School\Services\InstitutionService;

class InstitutionController extends Controller
{
    public function __construct(
        protected InstitutionService $institutionService,
        protected GetInstitutionIndexDataAction $getIndexDataAction,
        protected GetInstitutionShowDataAction $getShowDataAction,
        protected GetInstitutionCreateDataAction $getCreateDataAction,
        protected GetInstitutionEditDataAction $getEditDataAction,
        protected CreateInstitutionAction $createAction,
        protected UpdateInstitutionAction $updateAction,
        protected DeleteInstitutionAction $deleteAction,
        protected ToggleInstitutionStatusAction $toggleStatusAction,
    ) {}

    /**
     * Display a listing of institutions.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'status', 'type']);

        $data = $this->getIndexDataAction->execute($perPage, $filters);

        return Inertia::render('school::dashboard/Institution/Index', $data);
    }

    /**
     * Show the form for creating a new institution.
     */
    public function create(): Modal
    {
        $data = $this->getCreateDataAction->execute();

        return Inertia::modal('school::dashboard/Institution/Create', $data)
            ->baseRoute('school.institutions.index');
    }

    /**
     * Store a newly created institution.
     */
    public function store(StoreInstitutionRequest $request): RedirectResponse
    {
        $this->createAction->execute($request->validated());

        return redirect()
            ->route('school.institutions.index')
            ->with('success', 'Institution created successfully.');
    }

    /**
     * Display the specified institution.
     */
    public function show(Institution $institution): Response
    {
        $data = $this->getShowDataAction->execute($institution);

        return Inertia::render('school::dashboard/Institution/Show', $data);
    }

    /**
     * Show the form for editing the institution.
     */
    public function edit(Institution $institution): Modal
    {
        $data = $this->getEditDataAction->execute($institution);

        return Inertia::modal('school::dashboard/Institution/Edit', $data)
            ->baseRoute('school.institutions.index');
    }

    /**
     * Update the specified institution.
     */
    public function update(UpdateInstitutionRequest $request, Institution $institution): RedirectResponse
    {
        $this->updateAction->execute($institution, $request->validated());

        return redirect()
            ->route('school.institutions.index')
            ->with('success', 'Institution updated successfully.');
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(Institution $institution): Modal
    {
        $institution->loadCount(['departments', 'programs', 'employees']);

        return Inertia::modal('school::dashboard/Institution/Delete', [
            'institution' => (new InstitutionResource($institution))->resolve(),
        ])->baseRoute('school.institutions.index');
    }

    /**
     * Remove the specified institution.
     */
    public function destroy(Institution $institution): RedirectResponse
    {
        $this->deleteAction->execute($institution);

        return redirect()
            ->route('school.institutions.index')
            ->with('success', 'Institution deleted successfully.');
    }

    /**
     * Toggle institution status.
     */
    public function toggleStatus(Institution $institution): RedirectResponse
    {
        $this->toggleStatusAction->execute($institution);

        $status = $institution->status ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Institution {$status} successfully.");
    }
}
