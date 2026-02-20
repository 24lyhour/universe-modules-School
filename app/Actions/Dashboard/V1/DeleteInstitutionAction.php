<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Institution;

class DeleteInstitutionAction
{
    public function execute(Institution $institution): bool
    {
        return $institution->delete();
    }
}
