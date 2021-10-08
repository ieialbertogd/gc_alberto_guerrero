<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function index(Request $request, $any)
    {
        $urlBase = "/main/" . $any;
        return view('main',compact('urlBase') );
    }
}
