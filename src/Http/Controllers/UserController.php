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
        $userClass = app(UserContract::class);  
        $user = $userClass::findOrFail($id);
        
        $this->authorize('edit', $user);

        $title = __('filament::user.account', ['name' => $user->name]);

        return view('filament::users.edit', compact('title', 'user'));
    }
}