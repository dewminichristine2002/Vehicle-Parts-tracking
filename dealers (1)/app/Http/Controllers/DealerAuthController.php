<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
            'company_email' => 'required|email|unique:dealers,company_email',
            'company_mobile' => 'required|string|max:20',
            'email' => 'required|email|unique:dealers',
            'user_contact' => 'required|string|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'registered_at' => now(),
            'company_address' => $request->company_address,
            'company_mobile' => $request->company_mobile,
            'company_email' => $request->company_email,
            'owner_mobile' => $request->owner_mobile,
            'user_name' => $request->name,
            'user_designation' => $request->user_designation,
            'user_email' => $request->email,
            'user_contact' => $request->user_contact,
            'is_active' => true,
        ];

        if ($request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->store('public/logos');
            $data['company_logo'] = Storage::url($path);
        }

        Dealer::create($data);

        return redirect()->route('dealer.login.form')->with('success', 'Registration successful. Please log in.');
    }

    public function showLoginForm()
    {
        return view('dealer.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $dealer = Dealer::where('email', $credentials['email'])->first();

        // Check if dealer exists and is active
        if (!$dealer) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        if (!$dealer->is_active) {
            return back()->withErrors(['email' => 'This account has been deactivated.']);
        }

        if (Auth::guard('dealer')->attempt($credentials)) {
            $dealer = Auth::guard('dealer')->user();
            $dealer->last_login_at = now();
            $dealer->save();

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