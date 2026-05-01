<?php

namespace App;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait HasReceipt
{
    /**
     * Logika Generate Struk ke Gambar WebP
     */
    public function generateReceipt($transaction)
    {
        // Use store from transaction if Auth is not available (useful for seeding)
        $storeId = Auth::check() ? Auth::user()->store->id : $transaction->store_id;
        $store = Store::findOrFail($storeId);

        $driver = new Driver();
        $manager = new ImageManager($driver);

        $width = 350;
        $leftX = 35;
        $center = $width / 2;
        $fontPath = public_path('fonts/RobotoMono-Regular.ttf');

        // Initial height
        $tempHeight = 2000;
        $img = $manager->create($width, $tempHeight)->fill('white');

        $y = 30;

        // --- Store Header ---
        $img->text(strtoupper($store->name ?? 'STORE NAME'), $center, $y, function ($font) use ($fontPath) {
            $font->file($fontPath)->size(22)->align('center')->color('000');
        });

        $y += 25;
        $address = strtoupper($store->address ?? 'STORE ADDRESS');
        $charPerLine = floor(($width - ($leftX * 2)) / 7);
        $lines = explode("\n", wordwrap($address, $charPerLine, "\n", true));

        foreach ($lines as $line) {
            $img->text($line, $center, $y, function ($font) use ($fontPath) {
                $font->file($fontPath)->size(12)->align('center')->color('000');
            });
            $y += 16;
        }

        $y += 5;
        $img->text(receipt_line(), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 20;

        // --- Transaction Info ---
        $img->text('INV: ' . $transaction->invoice_code, $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 18;
        $img->text('DATE: ' . $transaction->created_at->format('d/m/Y H:i'), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 20;
        $img->text(receipt_line(), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 20;

        // --- Items ---
        foreach ($transaction->items as $item) {
            $name = strtoupper($item->product->name);
            $lines = receipt_wrap($name, 20);

            foreach ($lines as $i => $lineText) {
                if ($i === 0) {
                    $left = $lineText . ' x' . $item->qty;
                    $right = '$ ' . number_format($item->subtotal, 2, '.', ','); // Changed to $ format
                    $img->text(receipt_format($left, $right), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
                } else {
                    $img->text($lineText, $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
                }
                $y += 18;
            }
        }

        $y += 10;
        $img->text(receipt_line(), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 20;

        // --- Totals ---
        $img->text(receipt_format('TOTAL', '$ ' . number_format($transaction->total, 2, '.', ',')), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 18;
        $img->text(receipt_format('CASH', '$ ' . number_format($transaction->paid, 2, '.', ',')), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 18;
        $img->text(receipt_format('CHANGE', '$ ' . number_format($transaction->change, 2, '.', ',')), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 25;

        $img->text(receipt_line(), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 25;

        // --- Footer ---
        $img->text('THANK YOU', $center, $y, fn($font) => $font->file($fontPath)->size(12)->align('center')->color('000'));
        $y += 18;
        $img->text('PLEASE COME AGAIN', $center, $y, fn($font) => $font->file($fontPath)->size(11)->align('center')->color('000'));

        // --- Finalize Image ---
        $finalHeight = $y + 30;
        $img->crop($width, $finalHeight, 0, 0);

        $dir = storage_path('app/public/receipts');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileName = 'receipt-' . $transaction->invoice_code . '.webp';
        $finalPath = $dir . '/' . $fileName;

        $img->toWebp(30)->save($finalPath);

        return 'receipts/' . $fileName;
    }
}
