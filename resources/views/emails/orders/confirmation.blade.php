<x-mail::message>
# ¡Gracias por tu compra!

Hola {{ $order->customer_name }}, recibimos tu pedido **{{ $order->number }}** correctamente.

<x-mail::table>
| Producto | Cant. | Subtotal |
|:---------|:----:|--------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | ${{ number_format($item->subtotal, 0, ',', '.') }} |
@endforeach
</x-mail::table>

**Subtotal:** ${{ number_format($order->subtotal, 0, ',', '.') }}
**Envío:** {{ $order->shipping > 0 ? '$' . number_format($order->shipping, 0, ',', '.') : 'A coordinar' }}
**Total: ${{ number_format($order->total, 0, ',', '.') }}**

@if($order->payment_status === 'paid')
Tu pago fue aprobado. ¡Pronto preparamos tu pedido!
@else
El pago quedó **pendiente**. Nos pondremos en contacto para coordinar el pago y la entrega.
@endif

**Datos de envío**
{{ $order->customer_name }}
{{ $order->shipping_address }}
@if($order->customer_phone)
Tel: {{ $order->customer_phone }}
@endif

Gracias por elegir Feria.

Saludos,
{{ config('app.name') }}
</x-mail::message>
