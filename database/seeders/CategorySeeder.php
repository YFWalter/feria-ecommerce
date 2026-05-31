<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Cocina',
            'Organización del Hogar',
            'Bazar y Mesa',
            'Librería y Papelería',
            'Decoración',
            'Limpieza',
        ];

        foreach ($categories as $i => $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name'      => $name,
                    'image'     => null,
                    'is_active' => true,
                    'order'     => $i + 1,
                ]
            );
        }
    }
}
