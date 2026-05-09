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

        // Proteger admin de eliminación propia
        if ($user->role === 'admin') {
            return back()->withErrors([
                'password' => 'Los administradores no pueden eliminar sus propias cuentas. Contacta a otro administrador.'
            ]);
        }

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

        // Lógica diferenciada por rol
        $this->deleteUserData($user);

        // Logout user
        Auth::logout();

        // Delete user account
        $user->delete();

        // Redirect to home with success message
        return redirect()->route('home')
            ->with('success', 'Tu cuenta ha sido eliminada permanentemente. Esperamos verte de nuevo pronto.');
    }

    /**
     * Eliminar datos del usuario según su rol
     */
    private function deleteUserData($user)
    {
        switch ($user->role) {
            case 'editor':
                // Editor: elimina sus narraciones, seguidores, y todo lo relacionado
                $user->narraciones()->delete();
                $user->following()->delete();
                $user->followers()->delete();
                \App\Models\Feedback::where('email', $user->email)->delete();
                \App\Models\Feedback::where('nombre', $user->name)->delete();
                break;

            case 'lector':
                // Lector: elimina sus comentarios, seguidores que le siguen, y personas que sigue
                $user->following()->delete();
                $user->followers()->delete();
                \App\Models\Feedback::where('email', $user->email)->delete();
                \App\Models\Feedback::where('nombre', $user->name)->delete();
                break;

            default:
                // Por defecto: eliminar todo
                $user->narraciones()->delete();
                $user->following()->delete();
                $user->followers()->delete();
                \App\Models\Feedback::where('email', $user->email)->delete();
                \App\Models\Feedback::where('nombre', $user->name)->delete();
                break;
        }
    }
}
