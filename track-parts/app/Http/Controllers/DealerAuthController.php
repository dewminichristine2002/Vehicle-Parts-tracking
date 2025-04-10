<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DealerAuthController extends Controller
{
    
    public function showRegisterForm()
    {
        return view('dealer.register');
    }

    
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:dealers',
            'password' => 'required|min:6|confirmed',
        ]);

        Dealer::create([
            'name' => $request->name,
            'company_name' => $request->company_name, 
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'registered_at' => now(),
        ]);

        return redirect()->route('dealer.login.form')->with('success', 'Registration successful. Please log in.');
    }

    
    public function showLoginForm()
    {
        return view('dealer.login');
    }

    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('dealer')->attempt($credentials)) {
            return redirect()->route('dealer.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    
    public function dashboard()
    {
        return view('dealer.dashboard');
    }

    
    public function logout()
    {
        Auth::guard('dealer')->logout();
        return redirect()->route('dealer.login.form');
    }
}