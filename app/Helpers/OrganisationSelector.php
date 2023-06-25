<?php

namespace App\Helpers;

use App\Models\Organisation;
use Illuminate\Support\Facades\Session;

class OrganisationSelector
{
    public static function getSelectedOrganisation(): Organisation | false {
        if(Session::has('selectedOrganisationId')) {
            return Organisation::findOrFail(Session::get('selectedOrganisationId'));
        }

        return false;

    }






}
