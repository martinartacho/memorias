<?php

namespace App\Http\Controllers;

use App\Mail\SubscriberMagicLink;
use App\Models\Narracion;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriberController extends Controller
{
    /**
     * Muestra el formulario de suscripción.
     * Recibe el slug de la narración como parámetro opcional.
     */
    public function showForm(Request $request)
    {
        $narracion = null;
        if ($request->slug) {
            $narracion = Narracion::where('slug', $request->slug)->publicado()->first();
        }
        return view('suscripcion.form', compact('narracion'));
    }

    /**
     * Procesa el email enviado en el formulario.
     * Si el suscriptor ya existe y está verificado → envía magic link directo.
     * Si es nuevo o no verificado → crea/actualiza y envía enlace de verificación.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'Introduce un correo electrónico válido.',
        ]);

        $slug      = $request->input('slug');
        $narracion = $slug ? Narracion::where('slug', $slug)->publicado()->first() : null;

        $subscriber = Subscriber::firstOrCreate(
            ['email' => strtolower(trim($request->email))],
            ['token' => Subscriber::generateToken()]
        );

        // Si el token es antiguo o no tiene, regenerar
        if (!$subscriber->token) {
            $subscriber->token = Subscriber::generateToken();
            $subscriber->save();
        }

        Mail::to($subscriber->email)->send(
            new SubscriberMagicLink(
                subscriber:      $subscriber,
                redirectSlug:    $slug,
                narracionTitulo: $narracion?->titulo,
            )
        );

        return view('suscripcion.enviada', [
            'email'    => $subscriber->email,
            'narracion' => $narracion,
        ]);
    }

    /**
     * Verifica el suscriptor y establece la cookie de acceso.
     * Redirige a la narración si viene con slug.
     */
    public function acceder(Request $request, string $token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();

        // Marcar como verificado si aún no lo está
        if (!$subscriber->isVerified()) {
            $subscriber->verified_at = now();
            $subscriber->save();
        }

        $cookie = cookie(
            name:     'subscriber_token',
            value:    $subscriber->token,
            minutes:  60 * 24 * 365, // 1 año
            path:     '/',
            secure:   $request->isSecure(),
            httpOnly: true,
            sameSite: 'Lax',
        );

        $redirectSlug = $request->input('redirect');
        if ($redirectSlug) {
            return redirect()
                ->route('narraciones.show', $redirectSlug)
                ->withCookie($cookie);
        }

        return redirect()
            ->route('narraciones.index')
            ->withCookie($cookie)
            ->with('success', '¡Suscripción confirmada! Ya puedes leer el contenido exclusivo.');
    }

    /**
     * Muestra la pantalla de confirmación de baja.
     */
    public function showBaja(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();
        return view('suscripcion.baja', compact('subscriber'));
    }

    /**
     * Elimina al suscriptor y borra su cookie.
     */
    public function baja(Request $request, string $token)
    {
        $subscriber = Subscriber::where('token', $token)->first();

        if ($subscriber) {
            $subscriber->delete();
        }

        return redirect()
            ->route('narraciones.index')
            ->withoutCookie('subscriber_token')
            ->with('success', 'Te has dado de baja correctamente. ¡Hasta pronto!');
    }
}
