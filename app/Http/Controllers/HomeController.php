<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function homePage()
    {
        return Inertia::render( 'HomePage' );
    }
}
