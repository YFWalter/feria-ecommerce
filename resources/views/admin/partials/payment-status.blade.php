@php
    $map = [
        'pending' => ['Pendiente', 'bg-amber-50 text-amber-700'],
        'paid'    => ['Pagado',    'bg-green-50 text-green-700'],
        'failed'  => ['Fallido',   'bg-red-50 text-red-600'],
    ];
    [$label, $classes] = $map[$status] ?? [ucfirst($status), 'bg-gray-100 text-gray-700'];
@endphp
<span class="inline-block text-xs font-medium px-2.5 py-0.5 rounded-full {{ $classes }}">{{ $label }}</span>
