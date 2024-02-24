<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function debts()
    {
        return $this->hasMany(SaleDebt::class);
    }

    public function getCurrentBalanceAttribute()
    {
        return $this->initialBalance + $this->debts()->sum('debt') - $this->debts()->sum('paid') - $this->debts()->sum('discount');
    }

}
