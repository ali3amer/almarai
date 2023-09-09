<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function saleDebts()
    {
        return $this->hasMany(SaleDebt::class);
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class);
    }

}
