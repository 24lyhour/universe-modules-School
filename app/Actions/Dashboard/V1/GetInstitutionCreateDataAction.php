<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Models\Institution;

class GetInstitutionCreateDataAction
{
    public function execute(): array
    {
        return [
            'types' => Institution::getTypes(),
        ];
    }
}
