<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Institution;

class ToggleInstitutionStatusAction
{
    public function execute(Institution $institution): Institution
    {
        $institution->status = !$institution->status;
        $institution->save();

        return $institution;
    }
}
