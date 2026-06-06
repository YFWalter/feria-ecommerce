<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('products')
            ->when($request->q, fn($query, $q) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['slug']         = $this->uniqueSlug($data['name']);
        $data['is_active']    = $request->boolean('is_active');
        $data['show_on_home'] = $request->boolean('show_on_home');
        $data['order']        = $data['order'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->validateData($request);

        if ($data['name'] !== $category->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        }
        $data['is_active']    = $request->boolean('is_active');
        $data['show_on_home'] = $request->boolean('show_on_home');
        $data['order']        = $data['order'] ?? 0;

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'No se puede eliminar: la categoría tiene productos asociados.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría eliminada.');
    }

    /** Activa/desactiva la visibilidad de la categoría en la página de inicio. */
    public function toggleHome(Category $category)
    {
        $category->update(['show_on_home' => ! $category->show_on_home]);

        return back()->with('success', $category->show_on_home
            ? "“{$category->name}” ahora se muestra en el inicio."
            : "“{$category->name}” se quitó del inicio.");
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name'         => 'required|string|max:255',
            'order'        => 'nullable|integer|min:0',
            'image'        => 'nullable|image|max:2048',
            'is_active'    => 'nullable|boolean',
            'show_on_home' => 'nullable|boolean',
        ]);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (Category::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
