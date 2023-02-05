<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index(){

        include('examples/get_token.php');

    }
}
