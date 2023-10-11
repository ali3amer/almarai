<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function gifts()
    {
        return $this->hasMany(EmployeeGift::class);
    }

    public function debts()
    {
        return $this->hasMany(EmployeeDebt::class);
    }
}

