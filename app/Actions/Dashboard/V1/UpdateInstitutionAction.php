<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Facades\Auth;
use Modules\School\Models\Institution;

class UpdateInstitutionAction
{
    public function execute(Institution $institution, array $data): Institution
    {
        $data['updated_by'] = Auth::id();
        $institution->update($data);

        return $institution->fresh();
    }
}
