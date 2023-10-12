<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDebt extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function gift()
    {
        return $this->belongsTo(EmployeeGift::class);
    }
}
