<?php

namespace Database\Seeders;

use App\Models\Shoe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a Default Admin User for Web Login
        User::updateOrCreate(
            ['email' => 'admin@solesteps.com'],
            [
                'name' => 'Admin SoleSteps',
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. Create standard customer account for Flutter testing
        User::updateOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('user123'),
            ]
        );

        // 3. Seed Shoe products list
        $shoes = [
            [
                'name' => 'Nike Air Force 1 Low',
                'brand' => 'Nike',
                'category' => 'Sneakers',
                'price' => 1549000,
                'sizes' => [39, 40, 41, 42, 43],
                'stock' => 15,
                'image_url' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600',
                'description' => 'Sepatu legendaris Nike Air Force 1. Menampilkan siluet klasik berpadu dengan bahan kulit premium berwarna putih bersih yang tahan lama serta bantalan udara Air Sole untuk kenyamanan sepanjang hari.'
            ],
            [
                'name' => 'Adidas Ultraboost Light',
                'brand' => 'Adidas',
                'category' => 'Running',
                'price' => 2799000,
                'sizes' => [40, 41, 42, 43, 44],
                'stock' => 12,
                'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
                'description' => 'Adidas Ultraboost Light adalah sepatu lari teringan yang pernah ada dengan bantalan Boost legendaris. Dilengkapi upper Primeknit yang melukis kaki Anda serta cengkeraman Continental Rubber yang tangguh.'
            ],
            [
                'name' => 'Air Jordan 1 Retro High Chicago',
                'brand' => 'Jordan',
                'category' => 'Basketball',
                'price' => 3099000,
                'sizes' => [41, 42, 43, 44, 45],
                'stock' => 6,
                'image_url' => 'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=600',
                'description' => 'Air Jordan 1 Retro High klasik Chicago dengan warna ikonik merah, hitam, dan putih. Sepatu basket ikonik perpaduan sempurna antara warisan olahraga legendaris Michael Jordan dan gaya streetwear masa kini.'
            ],
            [
                'name' => 'Puma RS-X Geek Unisex',
                'brand' => 'Puma',
                'category' => 'Casual',
                'price' => 1799000,
                'sizes' => [39, 40, 41, 42, 43],
                'stock' => 18,
                'image_url' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600',
                'description' => 'Gaya retro-futuristik bersatu kembali dalam Puma RS-X Geek. Mengusung teknologi bantalan Running System (RS) berbalut desain sol tebal chunky dan kombinasi warna modern yang memikat.'
            ],
            [
                'name' => 'Nike Pegasus 40 Road Running',
                'brand' => 'Nike',
                'category' => 'Running',
                'price' => 1899000,
                'sizes' => [40, 41, 42, 43, 44],
                'stock' => 10,
                'image_url' => 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600',
                'description' => 'Sepatu lari legendaris harian Nike Air Zoom Pegasus 40 kembali hadir dengan peningkatan fit di bagian lidah dan kerah kaki. Dilengkapi dua unit Zoom Air yang responsif untuk dorongan ekstra.'
            ],
            [
                'name' => 'Adidas Forum Low Classic',
                'brand' => 'Adidas',
                'category' => 'Sneakers',
                'price' => 1649000,
                'sizes' => [38, 39, 40, 41, 42, 43],
                'stock' => 8,
                'image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600',
                'description' => 'Desain basket klasik tahun 80-an yang tak lekang oleh waktu. Menampilkan perpaduan panel kulit putih, tali strap pergelangan kaki yang khas, dan sol cup rubber yang memberikan kestabilan mumpuni.'
            ]
        ];

        foreach ($shoes as $shoe) {
            Shoe::updateOrCreate(
                ['name' => $shoe['name']],
                [
                    'brand' => $shoe['brand'],
                    'category' => $shoe['category'],
                    'price' => $shoe['price'],
                    'sizes' => $shoe['sizes'],
                    'stock' => $shoe['stock'],
                    'image_url' => $shoe['image_url'],
                    'description' => $shoe['description'],
                ]
            );
        }
    }
}
