<?php

namespace Alpine\Http\Controllers;

use Alpine\Contracts\User as UserContract;

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

        $title = __('alpine::admin.users');

        return view('alpine::users.index', compact('title'));
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

        $title = __('alpine::admin.user_edit', ['name' => $user->name]);

        return view('alpine::users.edit', compact('title', 'user'));
    }
}