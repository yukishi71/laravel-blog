<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    private $user;
    const LOCAL_STORAGE_FOLDER = 'public/avatars/';

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function show() {
        $user = $this->user->findOrFail(Auth::user()->id);
        return view('users.show')->with('user', $user);
    }

    public function edit() {
        $user = $this->user->findOrFail(Auth::user()->id);
        return view('users.edit')->with('user', $user);
    }

    public function update(Request $request) {
        $request->validate([
            'name'  => 'required|max:50',
            'email' => 'required|email|max:50|unique:users,email,' . Auth::user()->id,
            'avatar'=> 'mimes:jpeg,jpg,png,gif|max:1048'
        ]);
        // unique:table,column,except this ID
        $user        = $this->user->findOrFail(Auth::user()->id);
        $user->name  = $request->name;
        $user->email = $request->email;

        # If the user uploaded an avatar
        if ($request->avatar) {
            # If the user has an existing avatar, delete it from local storage.
            if($user->avatar) {
                $this->deleteAvatar($user->avatar);
            }

            # Save the new avatar
            $user->avatar = $this->saveAvatar($request);
        }

        $user->save();

        return redirect()->route('profile.show');
    }

    private function saveAvatar($request) {
        $avatar_name = time() . "." . $request->avatar->extension();
        $request->avatar->storeAs(self::LOCAL_STORAGE_FOLDER, $avatar_name);

        return $avatar_name;
    }


    private function deleteAvatar($avatar_name) {
        $avatar_path = self::LOCAL_STORAGE_FOLDER . $avatar_name;

        if(Storage::disk('local')->exists($avatar_path)){
           Storage::disk('local')->delete($avatar_path);
        }
    }
}

