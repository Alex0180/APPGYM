<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function register()
    {
        return view('clients.register');
    }

    public function list()
    {
        return view('clients.list');
    }
}

