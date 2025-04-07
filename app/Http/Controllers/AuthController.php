<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    //

    public function getLogin(){

        return view('Auth.login');
    }
}
