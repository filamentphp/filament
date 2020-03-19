<?php

namespace Filament\Http\Controllers;

use Filament\Contracts\User as UserContract;

class UserController extends Controller
{

    /**
     * Show the users.
     *
     * @return View
     */
    public function index()
    {
        $this->authorize('view', app(UserContract::class));

        $title = __('filament::admin.users');
        
        return view('filament::users.index', compact('title'));
    }

    /**
     * Show the user edit form.
     *
     * @param  int $id
     * @return View
     */
    public function edit($id)
    {
        $userModel = app(UserContract::class);  
        $user = $userModel::findOrFail($id);
        
        $this->authorize('edit', $user);

        $title = __('filament::admin.user_edit', ['name' => $user->name]);

        return view('filament::users.edit', compact('title', 'user'));
    }
}