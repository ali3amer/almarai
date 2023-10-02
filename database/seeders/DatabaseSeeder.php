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
                'employeeName' => 'Employee_'.$i,
                'salary' => '200000'
            ]);
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

        Safe::create([
            'initialBalance' => 0,
            'currentBalance' => 0
            ]);
        Bank::create([
            'bankName' => 'mbok',
            'accountName' => 'ali',
            'number' => '1234567',
            'initialBalance' => 0,
            'currentBalance' => 0,
        ]);
//  $permissions = ["create-banks",
//  "read-banks",
//  "update-banks",
//  "delete-banks",
//  "create-categories",
//  "read-categories",
//  "update-categories",
//  "delete-categories",
//  "create-clients",
//  "read-clients",
//  "update-clients",
//  "delete-clients",
//  "create-damageds",
//  "read-damageds",
//  "update-damageds",
//  "delete-damageds",
//  "create-employee_gifts",
//  "read-employee_gifts",
//  "update-employee_gifts",
//  "delete-employee_gifts",
//  "create-employees",
//  "read-employees",
//  "update-employees",
//  "delete-employees",
//  "create-expenses",
//  "read-expenses",
//  "update-expenses",
//  "delete-expenses",
//  "create-products",
//  "read-products",
//  "update-products",
//  "delete-products",
//  "create-purchase_debts",
//  "read-purchase_debts",
//  "update-purchase_debts",
//  "delete-purchase_debts",
//  "create-purchase_details",
//  "read-purchase_details",
//  "update-purchase_details",
//  "delete-purchase_details",
//  "create-purchases",
//  "read-purchases",
//  "update-purchases",
//  "delete-purchases",
//  "create-safe_details",
//  "read-safe_details",
//  "update-safe_details",
//  "delete-safe_details",
//  "create-safes",
//  "read-safes",
//  "update-safes",
//  "delete-safes",
//  "create-sale_debts",
//  "read-sale_debts",
//  "update-sale_debts",
//  "delete-sale_debts",
//  "create-sale_details",
//  "read-sale_details",
//  "update-sale_details",
//  "delete-sale_details",
//  "create-sale_returns",
//  "read-sale_returns",
//  "update-sale_returns",
//  "delete-sale_returns",
//  "create-sales",
//  "read-sales",
//  "update-sales",
//  "delete-sales",
//  "create-stores",
//  "read-stores",
//  "update-stores",
//  "delete-stores",
//   "create-suppliers",
//   "read-suppliers",
//   "update-suppliers",
//   "delete-suppliers",
//   "create-transfers",
//   "read-transfers",
//   "update-transfers",
//   "delete-transfers",
//   "create-users",
//   "read-users",
//   "update-users",
//   "delete-users"];
//
//  foreach ($permissions as $permission) {
//      $permissionId = Permission::create(['permission' => $permission]);
//      UserPermission::create(['user_id' => 1, 'permission_id' => $permissionId->id]);
//  }
    }
}
