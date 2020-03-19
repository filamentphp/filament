<?php

namespace Filament\Http\Controllers;

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
        $title = __('filament::admin.dashboard');

        return view('filament::dashboard', compact('title'));
    }
}