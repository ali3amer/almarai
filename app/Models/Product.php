<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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


    public function getStockAttribute()
    {
        return $this->initialStock + $this->purchaseDetails()->sum("quantity") - $this->saleDetails()->sum("quantity") - $this->damageds()->sum("quantity");
//        $purchaseQuantity = $this->purchaseDetails()->whereHas('purchase', function ($query) {
//            $query->whereDate('purchase_date', '<=', session("date"));
//        })->sum('quantity');
//
//        $saleQuantity = $this->saleDetails()->whereHas('sale', function ($query) {
//            $query->whereDate('sale_date', '<=', session("date"));
//        })->sum('quantity');
//
//        $damagedQuantity = $this->damageds()->whereDate('damaged_date', '<=', session("date"))->sum('quantity');
//
//        return $this->initialStock + $purchaseQuantity - $saleQuantity - $damagedQuantity;

    }

}
