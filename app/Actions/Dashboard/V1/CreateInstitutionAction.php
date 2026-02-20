<?php

namespace Modules\School\Actions\Dashboard\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\School\Models\Institution;

class CreateInstitutionAction
{
    public function execute(array $data): Institution
    {
        $data['uuid'] = (string) Str::uuid();
        $data['created_by'] = Auth::id();

        return Institution::create($data);
    }
}
