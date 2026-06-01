<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Indica si el stock de este pedido ya fue descontado (para restaurarlo
            // una sola vez si el pago se rechaza/cancela).
            $table->boolean('stock_reduced')->default(false)->after('mp_preference_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('stock_reduced');
        });
    }
};
