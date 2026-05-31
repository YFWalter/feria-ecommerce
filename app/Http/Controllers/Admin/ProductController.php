<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->q, fn($query, $q) =>
                $query->where(fn($sub) =>
                    $sub->where('name', 'like', "%{$q}%")->orWhere('sku', 'like', "%{$q}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['slug']      = $this->uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['featured']  = $request->boolean('featured');
        $data['images']    = $this->storeImages($request);

        Product::create($data);

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateData($request);

        if ($data['name'] !== $product->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        }
        $data['is_active'] = $request->boolean('is_active');
        $data['featured']  = $request->boolean('featured');

        $images = $product->images ?? [];

        // Eliminar imágenes marcadas para borrar.
        foreach ((array) $request->input('remove_images', []) as $path) {
            if (($key = array_search($path, $images)) !== false) {
                Storage::disk('public')->delete($path);
                unset($images[$key]);
            }
        }

        // Agregar nuevas imágenes subidas.
        $data['images'] = array_merge(array_values($images), $this->storeImages($request));

        $product->update($data);

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $product->delete();

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto eliminado.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'sku'           => 'nullable|string|max:100',
            'images.*'      => 'nullable|image|max:2048',
            'is_active'     => 'nullable|boolean',
            'featured'      => 'nullable|boolean',
        ]);
    }

    private function storeImages(Request $request): array
    {
        $paths = [];

        foreach ((array) $request->file('images', []) as $file) {
            if ($file) {
                $paths[] = $file->store('products', 'public');
            }
        }

        return $paths;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (Product::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
