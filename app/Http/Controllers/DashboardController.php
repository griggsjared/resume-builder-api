<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    public function show(): InertiaResponse
    {
        return Inertia::render('Dashboard');
    }
}
