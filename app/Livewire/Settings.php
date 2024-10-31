<?php

namespace App\Livewire;

use App\Models\EmployeeGift;
use App\Models\People;
use App\Models\PurchaseDebt;
use App\Models\SaleDebt;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use function PHPUnit\Framework\isJson;

class Settings extends Component
{
    use LivewireAlert;

    public $title = "الإعدادات";
    public bool $show = false;


    public $name = "Point Of Sale";
    public $barcode = false;
    public $batch = false;

    public $initialBalance = 0;
    public $capital = 0;
    public $expired_date = false;

    public $logo = "";

    public function mount()
    {
        $settings = Setting::first();
        if ($settings) {
            $this->name = $settings->name;
            $this->barcode = (bool)$settings->barcode;
            $this->batch = (bool)$settings->batch;
            $this->expired_date = (bool)$settings->expired_date;
        } else {
            Setting::create([
                "name" => "pos",
                "barcode" => false,
                "batch" => false,
                "expired_date" => false,
            ]);
        }

        $safe = \App\Models\Safe::first();

        if ($safe) {
            $this->initialBalance = $safe->initialBalance;
            $this->capital = $safe->capital;
        }
    }

    public function createSettings()
    {
        Setting::create([
            "name" => "pos",
            "barcode" => false,
            "batch" => false,
            "expired_date" => false,
        ]);
    }

    public function save()
    {

        Setting::first()->update([
            "name" => $this->name,
            "barcode" => $this->barcode,
            "batch" => $this->batch,
            "expired_date" => $this->expired_date,
        ]);

        $safe = \App\Models\Safe::first();

        if ($safe) {
            $safe->update([
                "capital" => $this->capital,
                "initialBalance" => $this->initialBalance,
                "startingDate" => session("date")
            ]);
        }

        $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

    }

    public function render()
    {

        return view('livewire.settings');
    }

//    public function fixData()
//    {
//        set_time_limit(180);
//        Artisan::call("migrate:fresh --seed");
//        $this->alert('success', 'تم تنظيف الجداول', ['timerProgressBar' => true]);
//        $tables = [
//            "settings" => "settings",
//            "safes" => "safes",
//            "banks" => "banks",
//            "stores" => "stores",
//            "categories" => "categories",
//            "products" => "products",
//            "prices" => "prices",
//            "damageds" => "damageds",
//            "deposit_debts" => "deposit_debts",
//            "employee_gifts" => "employee_gifts",
//            "expense_options" => "expense_options",
//            "expenses" => "expenses",
//            "purchases" => "purchases",
//            "purchase_debts" => "purchase_debts",
//            "purchase_details" => "purchase_details",
//            "purchase_returns" => "purchase_returns",
//            "sales" => "sales",
//            "sale_debts" => "sale_debts",
//            "sale_details" => "sale_details",
//            "sale_returns" => "sale_returns",
//            "transfers" => "transfers",
//            "withdraws" => "withdraws",
//            "days" => "days",
//        ];
//
//        $clients = [];
//        $suppliers = [];
//        $employees = [];
//        $deposits = [];
//
//        // جلب بيانات العملاء، الموردين، الموظفين، والودائع
//        $dataClients = DB::connection("db2")->table("clients")->get()->sortBy("id");
//        $dataSuppliers = DB::connection("db2")->table("suppliers")->get()->sortBy("id");
//        $dataEmployees = DB::connection("db2")->table("employees")->get()->sortBy("id");
//        $dataDeposits = DB::connection("db2")->table("deposits")->get()->sortBy("id");
//
//        // إدخال بيانات الأشخاص (عملاء، موردين، موظفين، ودائع)
//        foreach ($dataClients as $client) {
//            $people = People::firstOrCreate(
//                ["name" => $client->clientName, "type" => 'client'], // تحديد فريد للتحقق
//                [
//                    "phone" => $client->phone,
//                    "address" => $client->address,
//                    "initialDepositsBalance" => 0,
//                    "initialSalesBalance" => $client->initialBalance,
//                    "initialPurchasesBalance" => 0,
//                    "startingDate" => $client->startingDate,
//                    "cash" => $client->cash,
//                    "blocked" => $client->blocked,
//                    "note" => $client->note,
//                    "created_at" => $client->created_at,
//                    "updated_at" => $client->updated_at,
//                ]
//            );
//            $clients[$client->id]["oldId"] = $client->id;
//            $clients[$client->id]["newId"] = $people->id;
//        }
//
//        foreach ($dataSuppliers as $supplier) {
//            $people = People::firstOrCreate(
//                ["name" => $supplier->supplierName, "type" => 'supplier'],
//                [
//                    "phone" => $supplier->phone,
//                    "address" => $supplier->address,
//                    "initialDepositsBalance" => 0,
//                    "initialSalesBalance" => $supplier->initialSalesBalance,
//                    "initialPurchasesBalance" => $supplier->initialBalance,
//                    "startingDate" => $supplier->startingDate,
//                    "cash" => $supplier->cash,
//                    "blocked" => $supplier->blocked,
//                    "note" => $supplier->note,
//                    "created_at" => $supplier->created_at,
//                    "updated_at" => $supplier->updated_at,
//                ]
//            );
//            $suppliers[$supplier->id]["oldId"] = $supplier->id;
//            $suppliers[$supplier->id]["newId"] = $people->id;
//        }
//
//        foreach ($dataEmployees as $employee) {
//            $people = People::firstOrCreate(
//                ["name" => $employee->employeeName, "type" => 'employee'],
//                [
//                    "phone" => null,
//                    "address" => null,
//                    "initialDepositsBalance" => 0,
//                    "initialSalesBalance" => $employee->initialBalance,
//                    "initialPurchasesBalance" => 0,
//                    "startingDate" => $employee->startingDate,
//                    "cash" => false,
//                    "blocked" => false,
//                    "note" => null,
//                    "created_at" => $employee->created_at,
//                    "updated_at" => $employee->updated_at,
//                ]
//            );
//            $employees[$employee->id]["oldId"] = $employee->id;
//            $employees[$employee->id]["newId"] = $people->id;
//        }
//
//        foreach ($dataDeposits as $deposit) {
//            $people = People::firstOrCreate(
//                ["name" => $deposit->name, "type" => 'deposit'],
//                [
//                    "phone" => $deposit->phone,
//                    "address" => $deposit->address,
//                    "initialDepositsBalance" => $deposit->initialBalance,
//                    "initialSalesBalance" => 0,
//                    "initialPurchasesBalance" => 0,
//                    "startingDate" => $deposit->startingDate,
//                    "cash" => false,
//                    "blocked" => false,
//                    "note" => $deposit->note,
//                    "created_at" => $deposit->created_at,
//                    "updated_at" => $deposit->updated_at,
//                ]
//            );
//            $deposits[$deposit->id]["oldId"] = $deposit->id;
//            $deposits[$deposit->id]["newId"] = $people->id;
//        }
//        $this->alert('success', 'تم حفظ العملاء بنجاح', ['timerProgressBar' => true]);
//
//        // عملية نقل البيانات للجداول الأخرى
//        foreach ($tables as $table) {
//            $items = DB::connection("db2")->table($table)->get();
//
//            if ($items->isEmpty()) {
//                continue; // تخطي إذا لم يكن هناك بيانات
//            }
//
//            // تحويل العناصر إلى مصفوفة
//            $itemsArray = $items->map(function ($item) use ($table, $deposits, $employees, $suppliers, $clients) {
//                $item = (array)$item;
//
//                // استبدال client_id أو supplier_id أو employee_id بـ people_id
//                if (!empty($item['client_id'])) {
//                    $item['people_id'] = $clients[$item['client_id']]['newId'];
//                    unset($item['client_id']);
//                } elseif (!empty($item['supplier_id'])) {
//                    $item['people_id'] = $suppliers[$item['supplier_id']]['newId'];
//                    unset($item['supplier_id']);
//                } elseif (!empty($item['employee_id'])) {
//                    $item['people_id'] = $employees[$item['employee_id']]['newId'];
//                    unset($item['employee_id']);
//                } elseif (!empty($item['deposit_id'])) {
//                    $item['people_id'] = $deposits[$item['deposit_id']]['newId'];
//                    unset($item['deposit_id']);
//                }
//
//                if ($table == "sales" || $table == "sale_debts" || $table == "purchase_debts" || $table == "purchases") {
//                    unset($item['client_id']);
//
//                    unset($item['supplier_id']);
//
//                    unset($item['employee_id']);
//
//                    unset($item['deposit_id']);
//                }
//
//
//                // التحقق من الحقول المتعلقة بـ discount و service
//                if (isset($item['discount']) && floatval($item['discount']) > 0 && isset($item['type'])) {
//                    $item['amount'] = $item['discount'];
//                    $item['type'] = 'discount';
//                    unset($item['discount']);
//                } else {
//                    unset($item['discount']);
//                }
//
//                if (isset($item['service']) && isset($item['type'])) {
//                    unset($item['service']);
//                } else {
//                    unset($item['service']);
//                }
//
//                return $item;
//            })->toArray();
//
//            // التحقق من وجود البيانات قبل إدخالها
//            foreach ($itemsArray as $itemData) {
//                DB::connection('mysql')->table($table)->updateOrInsert(
//                    ['id' => $itemData['id']], // تحديد فريد لضمان عدم التكرار
//                    $itemData // البيانات المطلوب إدخالها
//                );
//            }
//        }
//
//        $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
//
//    }

    public function fixData()
    {
        $gifts = EmployeeGift::all();
        foreach ($gifts as $gift) {
            SaleDebt::create([
                'people_id' => $gift['people_id'],
                'type' => $gift['type'] == "salary" ? "discount" : $gift['type'],
                'amount' => floatval($gift['amount']),
                'payment' => $gift['payment'],
                'bank_id' => $gift['bank_id'],
                'bank' => $gift['bank'],
                'due_date' => $gift['due_date'],
                'note' => $gift['type'] == "salary" ? "خصم (مرتب)" : $gift['note'],
                'user_id' => auth()->id(),
                'created_at' => $gift['created_at'],
                'updated_at' => $gift['updated_at'],
            ]);

            $gift->delete();
        }
    }
}
