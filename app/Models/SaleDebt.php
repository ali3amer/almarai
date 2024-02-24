<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleDebt extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function gift()
    {
        return $this->hasOne(EmployeeGift::class);
    }
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d h:i:s');
    }

}


