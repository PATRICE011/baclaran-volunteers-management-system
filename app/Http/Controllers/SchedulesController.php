<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SchedulesController extends Controller
{
    //
    public function index(){

        $user = Auth::user();
        return view('admin_schedule',compact('user'));
    }
}
