<x-mail::message>
# Nuevo pedido recibido

Se registró un nuevo pedido: **{{ $order->number }}**

- **Cliente:** {{ $order->customer_name }}
- **Email:** {{ $order->customer_email }}
@if($order->customer_phone)
- **Teléfono:** {{ $order->customer_phone }}
@endif
- **Dirección:** {{ $order->shipping_address }}
@if($order->notes)
- **Notas:** {{ $order->notes }}
@endif

<x-mail::table>
| Producto | Cant. | Subtotal |
|:---------|:----:|--------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | ${{ number_format($item->subtotal, 0, ',', '.') }} |
@endforeach
</x-mail::table>

**Total: ${{ number_format($order->total, 0, ',', '.') }}**

<x-mail::button :url="route('admin.pedidos.show', $order)">
Ver pedido en el panel
</x-mail::button>
</x-mail::message>
