<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\ChangePasswordRequest;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateProfileRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        return view('users.index')->with('users', User::all());
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // `role` is not mass-assignable. Set it explicitly only after we have
        // a hashed password and a validated request from an admin.
        if ($request->boolean('is_admin')) {
            $user->makeAdmin();
        } else {
            $user->forceFill(['role' => 'employee'])->save();
        }

        session()->flash('success', 'User created.');

        return redirect(route('users.index'));
    }

    /* ---------- My profile (any authenticated user) ---------- */

    public function profile()
    {
        return view('users.profile')->with('user', auth()->user());
    }

    /** Edit profile details. UpdateProfileRequest restricts to admins. */
    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'about' => $request->about,
        ]);

        session()->flash('success', 'Profile updated.');

        return redirect()->route('users.profile');
    }

    /** Change own password — available to any authenticated user. */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = auth()->user();

        $user->forceFill([
            'password' => Hash::make($request->input('password')),
        ])->save();

        session()->flash('success', 'Password changed.');

        return redirect()->route('users.profile');
    }

    /* ---------- Admin editing another user account ---------- */

    public function editUser(User $user)
    {
        return view('users.edit')->with('user', $user);
    }

    public function updateUser(UpdateUserRequest $request, User $user)
    {
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        // Optional password reset — only when the admin actually filled the field.
        if ($request->filled('password')) {
            $user->forceFill(['password' => Hash::make($request->password)])->save();
        }

        // Role toggle (out-of-band on $fillable, so handled explicitly).
        if ($request->boolean('is_admin') && ! $user->isAdmin()) {
            $user->makeAdmin();
        } elseif (! $request->boolean('is_admin') && $user->isAdmin()) {
            // Don't let the last admin demote themselves into a non-admin via this form.
            if ($user->id === auth()->id()) {
                session()->flash('error', 'You can\'t remove your own admin access.');
                return redirect()->back();
            }
            $user->forceFill(['role' => 'employee'])->save();
        }

        session()->flash('success', 'User updated.');

        return redirect(route('users.index'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You can\'t delete your own account.');
            return redirect(route('users.index'));
        }

        $user->delete();
        session()->flash('success', 'User deleted.');

        return redirect(route('users.index'));
    }

    public function makeAdmin(User $user)
    {
        $user->makeAdmin();
        session()->flash('success', 'User promoted to admin.');

        return redirect(route('users.index'));
    }
}
