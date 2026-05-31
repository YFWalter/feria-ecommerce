<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'number', 'status', 'payment_status',
        'subtotal', 'shipping', 'total',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'notes',
        'mp_payment_id', 'mp_preference_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateNumber(): string
    {
        return strtoupper('FER-' . date('ymd') . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT));
    }
}
