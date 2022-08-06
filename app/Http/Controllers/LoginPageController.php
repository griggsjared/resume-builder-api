<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class LoginPageController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Login');
    }
}
