<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart  = session('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1',
        ]);

        $product  = Product::findOrFail($request->product_id);
        $quantity = (int) $request->get('quantity', 1);

        // Sin stock o producto inactivo: no se puede agregar.
        if (! $product->is_active || $product->stock < 1) {
            return back()->with('error', "\"{$product->name}\" no tiene stock disponible.");
        }

        $cart    = session('cart', []);
        $key     = 'product_' . $product->id;
        $current = $cart[$key]['quantity'] ?? 0;
        $newQty  = min($current + $quantity, $product->stock);

        // Ya tiene en el carrito todo el stock disponible.
        if ($newQty === $current) {
            return back()->with('error', "Ya tenés el máximo disponible de \"{$product->name}\" en el carrito (stock: {$product->stock}).");
        }

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $newQty;
            $cart[$key]['stock']    = $product->stock;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => (float) $product->price,
                'image'      => $product->first_image,
                'slug'       => $product->slug,
                'stock'      => $product->stock,
                'quantity'   => $newQty,
            ];
        }

        session(['cart' => $cart]);

        // Avisamos si se agregó menos de lo pedido por falta de stock.
        if ($newQty < $current + $quantity) {
            return back()->with('success', "Agregamos \"{$product->name}\" hasta el máximo disponible ({$product->stock} u.).");
        }

        return back()->with('success', "\"{$product->name}\" agregado al carrito.");
    }

    public function update(Request $request, string $rowId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = session('cart', []);

        if (isset($cart[$rowId])) {
            $cart[$rowId]['quantity'] = min($request->quantity, $cart[$rowId]['stock']);
            session(['cart' => $cart]);
        }

        return back();
    }

    public function remove(string $rowId)
    {
        $cart = session('cart', []);
        unset($cart[$rowId]);
        session(['cart' => $cart]);

        return back()->with('success', 'Producto eliminado del carrito.');
    }
}
