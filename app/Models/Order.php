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
        'mp_payment_id', 'mp_preference_id', 'stock_reduced',
    ];

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'shipping'      => 'decimal:2',
        'total'         => 'decimal:2',
        'stock_reduced' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateNumber(): string
    {
        return strtoupper('FER-' . date('ymd') . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT));
    }

    /**
     * Devuelve al stock las unidades del pedido. Idempotente: solo actúa si el
     * stock todavía estaba descontado (stock_reduced = true).
     */
    public function restoreStock(): void
    {
        if (! $this->stock_reduced) {
            return;
        }

        $this->loadMissing('items');

        foreach ($this->items as $item) {
            if ($item->product_id) {
                Product::whereKey($item->product_id)->increment('stock', $item->quantity);
            }
        }

        $this->stock_reduced = false;
        $this->save();
    }
}
