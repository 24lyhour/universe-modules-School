<?php

namespace Modules\School\Actions\Dashboard\V1;

use Modules\School\Http\Resources\Dashboard\V1\InstitutionResource;
use Modules\School\Models\Institution;

class GetInstitutionEditDataAction
{
    public function execute(Institution $institution): array
    {
        return [
            'institution' => (new InstitutionResource($institution))->resolve(),
            'types' => Institution::getTypes(),
        ];
    }
}
