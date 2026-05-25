<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Lista de usuarios (solo admin).
     */
    public function index()
    {
        // Solo el admin puede gestionar usuarios
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $users = User::orderBy('role')->orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Cambiar el rol de un usuario (solo admin).
     */
    public function updateRole(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // No permitir que el admin se cambie su propio rol
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes cambiar tu propio rol.');
        }

        $request->validate([
            'role' => 'required|in:lector,editor,admin',
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Rol de {$user->name} actualizado a {$request->role}.");
    }
}
