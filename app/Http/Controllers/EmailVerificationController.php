<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Verify email
     */
    public function verify($token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Token de verificación inválido o expirado.');
        }

        // Marcar email como verificado
        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->save();

        return redirect()->route('login')
            ->with('success', '¡Email verificado exitosamente! Ahora puedes iniciar sesión.');
    }

    /**
     * Show verification notice
     */
    public function notice()
    {
        return view('auth.verify-email');
    }

    /**
     * Resend verification email
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return back()->with('success', 'Este email ya está verificado.');
        }

        // Generar nuevo token
        $user->email_verification_token = \Illuminate\Support\Str::random(60);
        $user->save();

        // Guardar en sesión para desarrollo
        session(['verification_token' => $user->email_verification_token]);
        session(['verification_email' => $user->email]);

        return back()->with('success', 'Email de verificación reenviado. Revisa tu bandeja de entrada.');
    }
}
