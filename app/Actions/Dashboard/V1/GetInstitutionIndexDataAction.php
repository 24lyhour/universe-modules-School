<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\InstitutionResource;
use Modules\School\Models\Institution;

class GetInstitutionIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
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

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status'] === '1' || $filters['status'] === 'active');
        }

        $institutions = $query->latest()->paginate($perPage);

        $stats = [
            'total' => Institution::count(),
            'active' => Institution::where('status', true)->count(),
            'inactive' => Institution::where('status', false)->count(),
            'universities' => Institution::where('type', 'university')->count(),
            'institutes' => Institution::where('type', 'institute')->count(),
            'colleges' => Institution::where('type', 'college')->count(),
        ];

        return [
            'institutions' => [
                'data' => InstitutionResource::collection($institutions)->resolve(),
                'meta' => [
                    'current_page' => $institutions->currentPage(),
                    'last_page' => $institutions->lastPage(),
                    'per_page' => $institutions->perPage(),
                    'total' => $institutions->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => $stats,
            'types' => Institution::getTypes(),
        ];
    }
}
