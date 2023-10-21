<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Bank;
use App\Models\Category;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Safe;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LaratrustSeeder::class);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        $user = User::create([
            'name' => 'admin',
            'username' => 'admin',
            'password' => Hash::make(123456),
        ]);

        $user->addRole('super_admin');

        for ($i = 1; $i <= 2; $i++) {
            Category::create(['categoryName' => 'category_' . $i]);

            Store::create(['storeName' => 'store_' . $i]);

            Employee::create([
                'employeeName' => 'Employee_' . $i,
                'initialBalance' => 1000,
                'salary' => '200000'
            ]);

            Client::create([
                'clientName' => 'client_' . $i,
                'phone' => '0100200300',
                'initialBalance' => 1000,
                'startingDate' => date('Y-m-d'),
                'blocked' => false,
            ]);

            Supplier::create([
                'supplierName' => 'supplier_' . $i,
                'phone' => '0100200300',
                'initialBalance' => 1000,
                'initialSalesBalance' => 1000,
                'startingDate' => date('Y-m-d'),
                'blocked' => false,
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

        Safe::create([
            'initialBalance' => 0,
        ]);
        Bank::create([
            'bankName' => 'mbok',
            'accountName' => 'ali',
            'number' => '1234567',
            'initialBalance' => 0,
        ]);
    }
}
