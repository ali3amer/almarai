<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function damageds()
    {
        return $this->hasMany(Damaged::class);
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }


    public function getStockBeforeDate($date)
    {
        $initialStock = $this->initialStock;

        // جلب مجموع الكميات من المشتريات قبل التاريخ
        $purchasesBeforeDate = $this->purchaseDetails()
            ->whereHas('purchase', function ($query) use ($date) {
                $query->where('due_date', '<=', $date);
            })->sum('quantity');

        // جلب مجموع الكميات من المبيعات قبل التاريخ
        $salesBeforeDate = $this->saleDetails()
            ->whereHas('sale', function ($query) use ($date) {
                $query->where('due_date', '<=', $date);
            })->sum('quantity');

        // جلب مجموع الكميات من مرتجعات المبيعات قبل التاريخ
        $saleReturnsBeforeDate = $this->saleReturns()
            ->whereHas('sale', function ($query) use ($date) {
                $query->where('due_date', '<=', $date);
            })->sum('quantity');

        // جلب مجموع الكميات من مرتجعات المشتريات قبل التاريخ
        $purchaseReturnsBeforeDate = $this->purchaseReturns()
            ->whereHas('purchase', function ($query) use ($date) {
                $query->where('due_date', '<=', $date);
            })->sum('quantity');

        // جلب مجموع الكميات من المنتجات التالفة قبل التاريخ
        $damagedBeforeDate = $this->damageds()
            ->where('due_date', '<=', $date)
            ->sum('quantity');

        $settlementsIncrementBeforeDate = $this->settlements()
            ->where('due_date', '<=', $date)->where('type', 'increment')
            ->sum('quantity');

        $settlementsDecrementBeforeDate = $this->settlements()
            ->where('due_date', '<=', $date)->where('type', 'decrement')
            ->sum('quantity');

        // حساب المخزون قبل التاريخ
        return $initialStock
            + $purchasesBeforeDate
            - $salesBeforeDate
            + $saleReturnsBeforeDate
            - $purchaseReturnsBeforeDate
            - $damagedBeforeDate
            + $settlementsIncrementBeforeDate
            - $settlementsDecrementBeforeDate;
    }

    public function getProductMovements()
    {
        $movements = [];

        // تفاصيل المبيعات (صادر)
        $salesDetails = SaleDetail::select(
            DB::raw("'sales' as tableName"),
            DB::raw('quantity as expense'),
            DB::raw('0 as income'),
            'sale_id as invoice_id',
            'sales.due_date as due_date',
            DB::raw('CONCAT("مبيعات للفاتوره رقم #", sale_id, " | ", people.name) as note')
        )
            ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
            ->join('people', 'sales.people_id', '=', 'people.id')
            ->where('sale_details.product_id', $this->id);

        // تفاصيل المشتريات (وارد)
        $purchaseDetails = PurchaseDetail::select(
            DB::raw("'purchases' as tableName"),
            DB::raw('0 as expense'),
            DB::raw('quantity as income'),
            'purchase_id as invoice_id',
            'purchases.due_date as due_date',
            DB::raw('CONCAT("مشتريات للفاتوره رقم #", purchase_id, " | ", people.name) as note')
        )
            ->join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
            ->join('people', 'purchases.people_id', '=', 'people.id')
            ->where('purchase_details.product_id', $this->id);

        // مرتجعات المبيعات (وارد)
        $saleReturns = SaleReturn::select(
            DB::raw("'sale_returns' as tableName"),
            DB::raw('0 as expense'),
            DB::raw('quantity as income'),
            'sale_id as invoice_id',
            'sales.due_date as due_date',
            DB::raw('CONCAT("مرتجعات مبيعات للفاتوره رقم #", sale_id, " | ", people.name) as note')
        )
            ->join('sales', 'sales.id', '=', 'sale_returns.sale_id')
            ->join('people', 'sales.people_id', '=', 'people.id')
            ->where('sale_returns.product_id', $this->id);

        // مرتجعات المشتريات (صادر)
        $purchaseReturns = PurchaseReturn::select(
            DB::raw("'purchase_returns' as tableName"),
            DB::raw('quantity as expense'),
            DB::raw('0 as income'),
            'purchase_id as invoice_id',
            'purchases.due_date as due_date',
            DB::raw('CONCAT("مرتجعات مشتريات للفاتوره رقم #", purchase_id, purchase_id, " | ", people.name) as note')
        )
            ->join('purchases', 'purchases.id', '=', 'purchase_returns.purchase_id')
            ->join('people', 'purchases.people_id', '=', 'people.id')
            ->where('purchase_returns.product_id', $this->id);


        $damageds = Damaged::select(
            DB::raw("'damaged' as tableName"),
            DB::raw('quantity as expense'),
            DB::raw('0 as income'),
            DB::raw('null as invoice_id'),
            'damageds.due_date as due_date',
            DB::raw(" 'كمية تالفه' as note")
        )
            ->where('damageds.product_id', $this->id);

        $settlementsDecrement = Settlement::select(
            DB::raw("'settlements' as tableName"),
            DB::raw('quantity as expense'),
            DB::raw('0 as income'),
            DB::raw('null as invoice_id'),
            'settlements.due_date as due_date',
            DB::raw("note as note")
        )
            ->where('settlements.product_id', $this->id)->where('type', 'decrement');

        $settlementsIncrement = Settlement::select(
            DB::raw("'settlements' as tableName"),
            DB::raw('0 as expense'),
            DB::raw('quantity as income'),
            DB::raw('null as invoice_id'),
            'settlements.due_date as due_date',
            DB::raw("note as note")
        )
            ->where('settlements.product_id', $this->id)->where('type', 'increment');


        // استخدام union مع ترتيب الحركات حسب التاريخ
        return $salesDetails
            ->union($purchaseDetails)
            ->union($saleReturns)
            ->union($purchaseReturns)
            ->union($damageds)
            ->union($settlementsIncrement)
            ->union($settlementsDecrement)
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function getPrice($date = null)
    {
        $price = $this->prices()
            ->where("due_date", "<=", $date)
            ->orderBy('due_date', 'desc')
            ->first();
        if ($price) {
            return $this->prices()
                ->where("due_date", "<=", $date)
                ->orderBy('due_date', 'desc')
                ->first()->purchase_price;
        } else {
            return $this->purchase_price;
        }
    }

    public function getStockAttribute()
    {
        return $this->initialStock + $this->purchaseDetails()->sum("quantity") - $this->saleDetails()->sum("quantity") + $this->saleReturns()->sum("quantity") - $this->purchaseReturns()->sum("quantity") - $this->damageds()->sum("quantity") + $this->settlements()->where('type', 'increment')->sum('quantity') - $this->settlements()->where('type', 'decrement')->sum('quantity');
    }

}
