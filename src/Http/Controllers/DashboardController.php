<?php

namespace Alpine\Http\Controllers;

use App\User;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     *
     * @return View
     */
    public function __invoke()
    {
        $title = __('alpine::admin.dashboard');

        return view('alpine::dashboard', compact('title'));
    }
}