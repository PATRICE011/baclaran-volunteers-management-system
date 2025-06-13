<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class MinistryController extends Controller
{
    //
    public function index(){
        $user = Auth::user();
        return view('admin_ministries',compact('user'));
    }
}
