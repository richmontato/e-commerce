<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = (array) $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        $password = isset($validated['password']) && is_string($validated['password']) ? $validated['password'] : '';
        if ($user) {
            $user->update([
                'password' => Hash::make($password),
            ]);
            return back()->with('status', 'password-updated');
        }
        return back()->withErrors(['user' => 'User not authenticated.']);
    }
}
