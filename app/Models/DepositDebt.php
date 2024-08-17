<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositDebt extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }
}
