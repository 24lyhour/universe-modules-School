<?php

namespace Modules\School\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Modules\School\Models\Institution;

class InstitutionService
{
    /**
     * Get paginated institutions with filters.
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Institution::withCount(['departments', 'programs', 'employees']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new institution.
     */
    public function create(array $data): Institution
    {
        $data['uuid'] = (string) Str::uuid();
        return Institution::create($data);
    }

    /**
     * Update an institution.
     */
    public function update(Institution $institution, array $data): Institution
    {
        $institution->update($data);
        return $institution->fresh();
    }

    /**
     * Delete an institution.
     */
    public function delete(Institution $institution): bool
    {
        return $institution->delete();
    }

    /**
     * Get institution statistics.
     */
    public function getStats(): array
    {
        return [
            'total' => Institution::count(),
            'active' => Institution::where('status', true)->count(),
            'inactive' => Institution::where('status', false)->count(),
            'universities' => Institution::where('type', 'university')->count(),
            'institutes' => Institution::where('type', 'institute')->count(),
            'colleges' => Institution::where('type', 'college')->count(),
        ];
    }

    /**
     * Update institution status.
     */
    public function updateStatus(Institution $institution, bool $status): Institution
    {
        $institution->status = $status;
        $institution->save();
        return $institution;
    }

    /**
     * Find institution by UUID.
     */
    public function findByUuid(string $uuid): ?Institution
    {
        return Institution::where('uuid', $uuid)->first();
    }
}
