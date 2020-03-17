<?php

namespace Alpine\Http\Controllers;

use Alpine\Contracts\User as UserContract;

class UserController extends Controller
{
    /**
     * Show the user edit form.
     *
     * @param  int $user
     * @return View
     */
    public function edit($user)
    {
        $userModel = app(UserContract::class);
        $user = $userModel::findOrFail($user);
        $title = __('alpine::admin.user_edit', ['name' => $user->name]);

        return view('alpine::user.edit', compact('title', 'user'));
    }
}