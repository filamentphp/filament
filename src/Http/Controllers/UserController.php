<?php

namespace Alpine\Http\Controllers;

use Alpine\Contracts\User as UserContract;

class UserController extends Controller
{
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

        return view('alpine::user.edit', compact('title', 'user'));
    }
}