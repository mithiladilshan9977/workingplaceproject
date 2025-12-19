<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth; // optional for auto-login

class SignupController extends Controller
{
    // Show signup form
    public function showSignupForm()
    {
        return view('auth.signup');
    }

    // Handle form submission
    public function signup(Request $request)
    {
        // Validate the form input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed', 
        ]);

        // Create the user in the database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 1, // default active status
        ]);

        // Optional: auto-login after signup
        Auth::login($user);

        // Redirect to dashboard or home page
        return redirect()->intended('/dashboard')->with('success', 'Account created successfully!');
    }
}
