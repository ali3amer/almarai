<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function saleDebts()
    {
        return $this->hasMany(SaleDebt::class);
    }

    public function purchaseDebts()
    {
        return $this->hasMany(PurchaseDebt::class);
    }

    public function debts()
    {
        return $this->hasMany(SupplierDebt::class);
    }

}
