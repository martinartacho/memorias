<x-mail::message>
# {{ __('site.name') }}

@if($narracionTitulo)
Has solicitado acceso a la narración **«{{ $narracionTitulo }}»**.
@else
Gracias por suscribirte a **{{ __('site.name') }}**.
@endif

Haz clic en el siguiente enlace para acceder al contenido. El enlace no caduca.

<x-mail::button :url="$accessUrl" color="red">
Acceder ahora →
</x-mail::button>

---

**¿No has solicitado este acceso?**
Puedes ignorar este mensaje. Nadie más puede usar este enlace sin él.

**¿Quieres darte de baja?**
[Cancelar suscripción]({{ $unsubscribeUrl }})

*{{ __('site.name') }} · {{ __('site.subtitle') }}*
</x-mail::message>
