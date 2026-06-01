<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoProductImageSeeder extends Seeder
{
    /** Paletas [fondo, texto] que se van rotando por producto. */
    private array $palette = [
        ['#fde68a', '#92400e'], // amber
        ['#bfdbfe', '#1e3a8a'], // blue
        ['#bbf7d0', '#14532d'], // green
        ['#fbcfe8', '#831843'], // pink
        ['#ddd6fe', '#4c1d95'], // violet
        ['#fecaca', '#7f1d1d'], // red
        ['#a5f3fc', '#155e75'], // cyan
    ];

    public function run(): void
    {
        foreach (Product::all() as $product) {
            // No pisamos productos que ya tengan imágenes cargadas.
            if (! empty($product->images)) {
                continue;
            }

            [$bg, $fg] = $this->palette[$product->id % count($this->palette)];

            $path = 'products/demo-' . $product->id . '.svg';
            Storage::disk('public')->put($path, $this->placeholderSvg($product->name, $bg, $fg));

            $product->update(['images' => [$path]]);
        }
    }

    private function placeholderSvg(string $name, string $bg, string $fg): string
    {
        $lines      = $this->wrap($name, 16);
        $lineHeight = 30;
        $startY     = 200 - (count($lines) - 1) * $lineHeight / 2;
        $appName    = htmlspecialchars(config('app.name', 'Feria'), ENT_QUOTES);

        $texts = '';
        foreach ($lines as $idx => $line) {
            $y    = $startY + $idx * $lineHeight;
            $safe = htmlspecialchars($line, ENT_QUOTES);
            $texts .= "<text x=\"200\" y=\"{$y}\" font-family=\"Arial, sans-serif\" font-size=\"22\" font-weight=\"bold\" "
                . "fill=\"{$fg}\" text-anchor=\"middle\" dominant-baseline=\"middle\">{$safe}</text>";
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="400" height="400">'
            . "<rect width=\"400\" height=\"400\" fill=\"{$bg}\"/>"
            . $texts
            . "<text x=\"200\" y=\"365\" font-family=\"Arial, sans-serif\" font-size=\"15\" fill=\"{$fg}\" "
            . "text-anchor=\"middle\" opacity=\"0.55\">{$appName} · demo</text>"
            . '</svg>';
    }

    /** Parte el nombre en líneas de hasta $max caracteres (máx 4 líneas). */
    private function wrap(string $text, int $max): array
    {
        $words   = explode(' ', $text);
        $lines   = [];
        $current = '';

        foreach ($words as $word) {
            if ($current !== '' && strlen($current . ' ' . $word) > $max) {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $current === '' ? $word : $current . ' ' . $word;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return array_slice($lines, 0, 4);
    }
}
