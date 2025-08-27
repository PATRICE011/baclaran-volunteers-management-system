<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class  AttendanceController extends Controller
{
    //
    public function index(){
        $user = Auth::user();
        return view('attendance_tracking',compact('user'));
    }
}
