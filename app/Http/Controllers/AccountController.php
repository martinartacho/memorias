<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Show delete account confirmation page
     */
    public function delete()
    {
        return view('account.delete');
    }

    /**
     * Process account deletion
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        // Validate password confirmation
        $request->validate([
            'password' => 'required|string',
        ]);

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'La contraseña es incorrecta. La eliminación de cuenta no fue procesada.'
            ]);
        }

        // Delete user's narraciones
        $user->narraciones()->delete();

        // Delete user's follows (both as follower and followed)
        $user->following()->delete();
        $user->followers()->delete();

        // Delete user's feedback
        \App\Models\Feedback::where('email', $user->email)->delete();

        // Delete user's feedback submissions (if any)
        \App\Models\Feedback::where('nombre', $user->name)->delete();

        // Logout user
        Auth::logout();

        // Delete user account
        $user->delete();

        // Redirect to home with success message
        return redirect()->route('home')
            ->with('success', 'Tu cuenta ha sido eliminada permanentemente. Esperamos verte de nuevo pronto.');
    }
}
