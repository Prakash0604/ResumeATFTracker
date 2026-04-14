<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    protected string $redirectTo = '/dashboard';

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\pL\s\-\']+$/u',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->uncompromised(),
            ],
            'terms' => ['required', 'accepted'],
        ], [
            'name.required'        => 'Please enter your full name.',
            'name.regex'           => 'Name may only contain letters, spaces, hyphens, and apostrophes.',
            'email.required'       => 'Please enter your email address.',
            'email.email'          => 'Please enter a valid email address.',
            'email.unique'         => 'An account with this email already exists. Try logging in.',
            'password.required'    => 'Please choose a password.',
            'password.confirmed'   => 'Password confirmation does not match.',
            'password.min'         => 'Password must be at least 8 characters.',
            'terms.accepted'       => 'You must agree to the Terms of Service to create an account.',
        ]);

        $user = User::create([
            'name'     => trim($validated['name']),
            'email'    => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'plan'     => 'free',
        ]);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect($this->redirectTo)
            ->with('success', "Welcome to ResumeIQ, {$user->name}! 🎉 Upload your first resume to get started.");
    }
}
