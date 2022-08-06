<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        return view('spa');
    }
    public function test()
    {
        return 'it work!';
    }
}
