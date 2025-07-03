<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EventController extends Controller
{
    //

    public function index(){
        $user = Auth::user();
        return view ('event_management', compact('user'));
    }
}
