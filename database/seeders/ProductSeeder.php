<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // [nombre, precio, compare_price|null, stock, featured]
        $catalog = [
            'Cocina' => [
                ['Juego de Ollas Antiadherentes 5 piezas', 45000, 58000, 12, true],
                ['Set de Cuchillos de Acero 6 piezas', 18500, null, 20, false],
                ['Tabla de Picar de Bambú', 6800, null, 35, false],
                ['Sartén de Hierro 28cm', 22000, null, 8, false],
                ['Set de Utensilios de Silicona', 9900, 12500, 25, false],
            ],
            'Organización del Hogar' => [
                ['Caja Organizadora Plástica 30L', 8500, null, 40, true],
                ['Set 3 Cestos de Mimbre', 14200, 17000, 15, false],
                ['Organizador de Cajones Modular', 5600, null, 30, false],
                ['Perchas de Madera x12', 7800, null, 22, false],
                ['Zapatero de Tela 10 niveles', 11900, null, 5, false],
            ],
            'Bazar y Mesa' => [
                ['Juego de Vasos de Vidrio x6', 7200, null, 28, true],
                ['Vajilla de Porcelana 18 piezas', 38900, 45000, 10, false],
                ['Set de Cubiertos Acero Inox 24 piezas', 16500, null, 18, false],
                ['Jarra de Vidrio 1.5L', 4900, null, 33, false],
                ['Mantel de Algodón Rectangular', 8300, null, 14, false],
            ],
            'Librería y Papelería' => [
                ['Cuaderno Tapa Dura A4', 3200, null, 50, true],
                ['Set de Bolígrafos x10', 2800, null, 60, false],
                ['Resma de Papel A4 500 hojas', 6500, null, 40, false],
                ['Organizador de Escritorio', 5400, 6900, 16, false],
                ['Calculadora Científica', 9800, null, 3, false],
            ],
            'Decoración' => [
                ['Espejo Decorativo Redondo 50cm', 19500, 24000, 9, true],
                ['Set 3 Macetas de Cerámica', 8900, null, 20, false],
                ['Cuadro Decorativo Lienzo', 12400, null, 11, false],
                ['Guirnalda de Luces LED 5m', 6700, null, 26, false],
                ['Florero de Vidrio Soplado', 7600, null, 13, false],
            ],
            'Limpieza' => [
                ['Set de Limpieza 5 piezas', 9200, null, 30, true],
                ['Trapeador con Balde Centrifugador', 15800, 19000, 7, false],
                ['Pack 3 Microfibras', 3400, null, 45, false],
                ['Escoba + Pala Set', 5100, null, 0, false],
                ['Organizador de Productos de Limpieza', 6300, null, 19, false],
            ],
        ];

        $sku = 1000;

        foreach ($catalog as $categoryName => $products) {
            $category = Category::where('slug', Str::slug($categoryName))->first();

            if (! $category) {
                continue;
            }

            foreach ($products as [$name, $price, $comparePrice, $stock, $featured]) {
                Product::updateOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'category_id'   => $category->id,
                        'name'          => $name,
                        'description'   => "{$name}. Producto de calidad ideal para tu hogar. Encontralo en Feria al mejor precio.",
                        'price'         => $price,
                        'compare_price' => $comparePrice,
                        'stock'         => $stock,
                        'sku'           => 'FER-' . (++$sku),
                        'images'        => [],
                        'is_active'     => true,
                        'featured'      => $featured,
                    ]
                );
            }
        }
    }
}
