<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function update(ProfileUpdateRequest $request)
    {
        $request->user()->update($request->validated());
        return back()->with('status', 'Profile updated.');
    }
}
