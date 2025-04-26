<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VolunteersController extends Controller
{
    //
    public function index(){

        return view('admin_volunteer');
    }
}
