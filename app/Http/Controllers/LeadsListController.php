<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadsListController extends Controller
{

    public function leads()
    {
        include('examples/leads_add.php');
    }


}
