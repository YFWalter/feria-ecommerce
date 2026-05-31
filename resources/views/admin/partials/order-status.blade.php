@php
    $map = [
        'pending'    => ['Pendiente',  'bg-gray-100 text-gray-700'],
        'processing' => ['En proceso', 'bg-blue-50 text-blue-700'],
        'shipped'    => ['Enviado',    'bg-indigo-50 text-indigo-700'],
        'delivered'  => ['Entregado',  'bg-green-50 text-green-700'],
        'cancelled'  => ['Cancelado',  'bg-red-50 text-red-600'],
    ];
    [$label, $classes] = $map[$status] ?? [ucfirst($status), 'bg-gray-100 text-gray-700'];
@endphp
<span class="inline-block text-xs font-medium px-2.5 py-0.5 rounded-full {{ $classes }}">{{ $label }}</span>
