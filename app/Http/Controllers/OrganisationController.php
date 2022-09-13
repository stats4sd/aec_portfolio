<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\Team;
use Illuminate\Http\Request;

class OrganisationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function publicIndex()
    {
        return Organisation::withCount(['projects'])->get();
    }
}
