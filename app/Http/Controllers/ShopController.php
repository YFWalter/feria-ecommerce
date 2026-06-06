<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function home()
    {
        $categoriesQuery = Category::where('is_active', true)
            ->where('show_on_home', true)
            ->orderBy('order');

        if (($limit = (int) setting('home_categories_limit', 0)) > 0) {
            $categoriesQuery->take($limit);
        }

        $categories = $categoriesQuery->get();

        $featured   = Product::with('category')
            ->where('is_active', true)
            ->where('featured', true)
            ->latest()
            ->take(8)
            ->get();

        return view('shop.home', compact('categories', 'featured'));
    }

    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('order')->get();

        $query = Product::with('category')->where('is_active', true);

        if ($request->filled('categoria')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->categoria));
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $query->when($request->get('orden', 'recent'), function ($q, $orden) {
            match ($orden) {
                'price_asc'  => $q->orderBy('price'),
                'price_desc' => $q->orderByDesc('price'),
                default      => $q->latest(),
            };
        });

        $products = $query->paginate(16);

        return view('shop.index', compact('products', 'categories'));
    }

    public function show(string $slug)
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Product::with('category')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->latest()
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'related'));
    }
}
