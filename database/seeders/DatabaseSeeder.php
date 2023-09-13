<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Store;
use App\Models\Supplier;
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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        User::create([
            'name' => 'admin',
            'username' => 'admin',
            'password' => Hash::make(123456),
        ]);

        for ($i = 1; $i <= 2; $i++) {
            Category::create(['categoryName' => 'category_' . $i]);

            Store::create(['storeName' => 'store_' . $i]);

            Client::create([
                'clientName' => 'client_' . $i,
                'phone' => '0100200300',
                'initialBalance' => 1000,
                'currentBalance' => 1000,
            ]);

            Supplier::create([
                'supplierName' => 'supplier_' . $i,
                'address' => '11111',
                'phone' => '0100200300',
                'initialBalance' => 1000,
                'currentBalance' => 1000,
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            Product::create([
                'productName' => 'product_' . $i,
                'category_id' => 1,
                'store_id' => 1,
                'purchase_price' => 1000,
                'sale_price' => 2000,
                'stock' => 100,
            ]);
        }
    }
}
