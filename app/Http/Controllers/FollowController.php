<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Seguir a un autor
     */
    public function follow($authorId)
    {
        if (!Auth::check()) {
            // Si es AJAX, devolver JSON, si no, redirigir
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Debes estar autenticado'], 401);
            }
            return redirect()->route('login')->with('error', 'Debes estar autenticado para seguir autores');
        }

        $author = User::findOrFail($authorId);
        
        // No permitir seguirse a sí mismo
        if (Auth::id() == $authorId) {
            return response()->json(['error' => 'No puedes seguirte a ti mismo'], 400);
        }

        // Verificar si ya sigue al autor
        $existingFollow = Follow::where('follower_id', Auth::id())
                               ->where('followed_id', $authorId)
                               ->first();

        if ($existingFollow) {
            return response()->json(['message' => 'Ya sigues a este autor'], 200);
        }

        // Verificar si el autor requiere aprobación
        $requiresApproval = $author->follower_approval;
        
        // Crear nuevo seguimiento
        $follow = Follow::create([
            'follower_id' => Auth::id(),
            'followed_id' => $authorId,
            'followed_at' => now(),
            'approved' => !$requiresApproval, // Aprobado automáticamente si no requiere aprobación
        ]);

        // Si es AJAX, devolver JSON, si no, redirigir al dashboard
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $requiresApproval 
                    ? 'Solicitud de seguimiento enviada a ' . $author->name . '. Espera su aprobación.'
                    : 'Ahora sigues a ' . $author->name,
                'following' => true,
                'approved' => !$requiresApproval,
                'followers_count' => $author->followers()->count()
            ], 200);
        }
        
        return redirect()->route('dashboard')
            ->with('success', $requiresApproval 
                ? '¡Solicitud de seguimiento enviada a ' . $author->name . '! Espera su aprobación.'
                : '¡Ahora sigues a ' . $author->name . '!');
    }

    /**
     * Dejar de seguir a un autor
     */
    public function unfollow($authorId)
    {
        if (!Auth::check()) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Debes estar autenticado'], 401);
            }
            return redirect()->route('login')->with('error', 'Debes estar autenticado para seguir autores');
        }

        $author = User::findOrFail($authorId);

        // Buscar y eliminar seguimiento
        $follow = Follow::where('follower_id', Auth::id())
                       ->where('followed_id', $authorId)
                       ->first();

        if (!$follow) {
            return response()->json(['message' => 'No sigues a este autor'], 200);
        }

        $follow->delete();

        // Si es AJAX, devolver JSON, si no, redirigir al dashboard
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Has dejado de seguir a ' . $author->name,
                'following' => false,
                'followers_count' => $author->followers()->count()
            ], 200);
        }
        
        return redirect()->route('dashboard')
            ->with('success', 'Has dejado de seguir a ' . $author->name . '!');
    }

    /**
     * Alternar seguimiento (follow/unfollow)
     */
    public function toggle($authorId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Debes estar autenticado'], 401);
        }

        $author = User::findOrFail($authorId);
        
        // No permitir seguirse a sí mismo
        if (Auth::id() == $authorId) {
            return response()->json(['error' => 'No puedes seguirte a ti mismo'], 400);
        }

        $existingFollow = Follow::where('follower_id', Auth::id())
                               ->where('followed_id', $authorId)
                               ->first();

        if ($existingFollow) {
            // Dejar de seguir
            $existingFollow->delete();
            $following = false;
            $message = 'Has dejado de seguir a ' . $author->name;
        } else {
            // Seguir
            Follow::create([
                'follower_id' => Auth::id(),
                'followed_id' => $authorId,
            ]);
            $following = true;
            $message = 'Ahora sigues a ' . $author->name;
        }

        return response()->json([
            'message' => $message,
            'following' => $following,
            'followers_count' => $author->followers()->count(),
            'author_name' => $author->name
        ], 200);
    }

    /**
     * Obtener lista de autores que sigue el usuario
     */
    public function following()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Debes estar autenticado'], 401);
        }

        $following = Auth::user()->following()
            ->withCount('followers')
            ->get()
            ->map(function ($author) {
                return [
                    'id' => $author->id,
                    'name' => $author->name,
                    'email' => $author->email,
                    'followers_count' => $author->followers_count,
                    'narraciones_count' => $author->narraciones()->where('estado', 'publicado')->count(),
                    'followed_at' => $author->pivot->created_at
                ];
            });

        return response()->json($following, 200);
    }

    /**
     * Obtener lista de seguidores del usuario
     */
    public function followers()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Debes estar autenticado'], 401);
        }

        $followers = Auth::user()->followers()
            ->withCount('following')
            ->get()
            ->map(function ($follower) {
                return [
                    'id' => $follower->id,
                    'name' => $follower->name,
                    'email' => $follower->email,
                    'following_count' => $follower->following_count,
                    'followed_you_at' => $follower->pivot->created_at
                ];
            });

        return response()->json($followers, 200);
    }
}
