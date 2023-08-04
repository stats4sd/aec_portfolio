<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminPanelController extends Controller
{

    public function show()
    {
        return view('admin-panel.show');
    }

}
