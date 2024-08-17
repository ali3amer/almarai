<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function depositDebts()
    {
        return $this->hasMany(DepositDebt::class);
    }

    public function getCurrentBalanceAttribute()
    {
        return $this->initialBalance + $this->depositDebts()->where("type", "pay")->sum("amount") - $this->depositDebts()->where("type", "debt")->sum("amount");
    }
}
