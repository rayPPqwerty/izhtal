<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function home()
    {
        return view('/home');
    }
    public function documents()
    {
        return view('/documents');
    }

    public function skating()
    {
        return view('/skating');
    }

    public function contacts()
    {
        return view('/contacts');
    }

    public function gallery()
    {
        return view('/gallery');
    }
}
