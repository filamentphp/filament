<?php

namespace Filament\Http\Controllers;

use Spatie\Permission\Contracts\Role as RoleContract;

class RoleController extends Controller
{
    /**
     * Show the roles.
     *
     * @return View
     */
    public function index()
    {
        $this->authorize('view', app(RoleContract::class));

        $title = __('filament::admin.roles');
        
        return view('filament::roles.index', compact('title'));
    }
}