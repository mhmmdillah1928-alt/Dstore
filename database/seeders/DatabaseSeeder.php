<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Apple', 'Samsung', 'Xiaomi', 'Oppo', 'Vivo', 'Realme', 'ASUS', 'Infinix', 'Google', 'OnePlus', 'Nokia', 'Huawei'] as $b) {
            Brand::firstOrCreate(['slug' => Str::slug($b)], ['name' => $b]);
        }

        Setting::set('commission_percent', 5);

        User::create(['name' => 'Administrator', 'email' => 'admin@mail.com', 'password' => 'password', 'phone' => '081200000001', 'role' => 'admin']);
        $seller = User::create(['name' => 'Budi Seller', 'email' => 'seller@mail.com', 'password' => 'password', 'phone' => '081200000002', 'role' => 'seller']);
        User::create(['name' => 'Citra Customer', 'email' => 'customer@mail.com', 'password' => 'password', 'phone' => '081200000003', 'role' => 'customer']);

        $store = Store::create([
            'user_id' => $seller->id, 'store_name' => 'Budi Gadget Store',
            'address' => 'Jl. Merdeka No. 10, Jakarta Pusat', 'is_verified' => true,
        ]);

        $samples = [
            ['iPhone 13 128GB', 'apple', 'used', 9500000, 4, 128, 4, 174],
            ['iPhone 15 Pro 256GB', 'apple', 'new', 19999000, 3, 256, 8, 187],
            ['Samsung Galaxy S23 256GB', 'samsung', 'new', 11999000, 5, 256, 8, 168],
            ['Samsung Galaxy A54 128GB', 'samsung', 'used', 3800000, 2, 128, 8, 202],
            ['Xiaomi Redmi Note 13 Pro', 'xiaomi', 'new', 3999000, 10, 256, 8, 187],
            ['Oppo Reno 11 5G', 'oppo', 'new', 6499000, 6, 256, 8, 184],
            ['Vivo V30 5G', 'vivo', 'used', 4500000, 3, 256, 12, 186],
            ['Realme GT Neo 5', 'realme', 'new', 5999000, 7, 256, 12, 199],
            ['ASUS ROG Phone 7', 'asus', 'used', 9800000, 1, 512, 16, 239],
        ];
        foreach ($samples as [$name, $brand, $cond, $price, $stock, $storage, $ram, $weight]) {
            Product::create([
                'store_id' => $store->id, 'brand_id' => Brand::where('slug', $brand)->value('id'),
                'name' => $name, 'slug' => Str::slug($name) . '-' . Str::lower(Str::random(5)),
                'condition' => $cond, 'price' => $price, 'stock' => $stock, 'weight' => $weight,
                'ram' => $ram, 'storage' => $storage,
                'description' => "$name kondisi " . ($cond === 'new' ? 'baru segel, garansi resmi.' : 'mulus, fullset, bekas pemakaian normal.'),
            ]);
        }
    }
}
