<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vertical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function showRegisterForm()
    {
        $verticals = Vertical::all();
        return view('auth.register', compact('verticals'));
    }

    public function register(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'username'     => 'required|string|max:50|unique:users',
            'full_name'    => 'required|string|max:100',
            'password'     => 'required|string|min:6|confirmed',
            'role'         => 'required|in:admin,manager,vm,nfo,client',
            'vertical_id'  => 'nullable|exists:verticals,id', // Nullable if it's not required
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create the user
        $user = User::create([
            'username'    => $request->username,
            'full_name'   => $request->full_name,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'vertical_id' => $request->vertical_id,
        ]);

        // Log the user in
        Auth::login($user);

        // Redirect to dashboard
        return redirect()->intended('dashboard')
            ->with('success', 'Registration successful!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
