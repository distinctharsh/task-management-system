<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Role;
use App\Models\Vertical;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user() || !auth()->user()->isManager()) {
            return redirect()->route('home')->with('error', 'Access denied.');
        }
        $users = User::with('role')->get();
        $perPage = 'all';
        $roles = Role::all();
        $verticals = Vertical::all();
        return view('users.index', compact('users', 'perPage', 'roles', 'verticals'));
    }


    public function edit(User $user)
    {
        $roles = Role::all();
        $verticals = Vertical::all();
        return view('users.edit', compact('user', 'roles', 'verticals'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = $request->only('full_name', 'username', 'role_id');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->user()->id) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'username'     => 'required|string|max:50|unique:users',
            'full_name'    => 'required|string|max:100',
            'password'     => 'required|string|min:6|confirmed',
            'role_id'      => 'required|exists:roles,id',
            'vertical_id'  => 'nullable|exists:verticals,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.index')
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'username'    => $request->username,
            'full_name'   => $request->full_name,
            'password'    => \Hash::make($request->password),
            'role_id'     => $request->role_id,
            'vertical_id' => $request->vertical_id,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }
}
