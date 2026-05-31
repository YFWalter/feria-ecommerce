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
        $cart     = session('cart', []);
        $key      = 'product_' . $product->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = min(
                $cart[$key]['quantity'] + $quantity,
                $product->stock
            );
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => (float) $product->price,
                'image'      => $product->first_image,
                'slug'       => $product->slug,
                'stock'      => $product->stock,
                'quantity'   => min($quantity, $product->stock),
            ];
        }

        session(['cart' => $cart]);

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
