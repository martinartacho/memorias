<x-mail::message>
# Memorias sin orden

@if($narracionTitulo)
Has solicitado acceso a la narración **«{{ $narracionTitulo }}»**.
@else
Gracias por suscribirte a **Memorias sin orden**.
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

*Memorias sin orden · Colección literaria digital*
</x-mail::message>
