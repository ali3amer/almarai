<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function __construct()
    {
        parent::__construct();

        if (self::count() === 0) {
            $this->name = 'pos';
            $this->barcode = false;
            $this->batch = false;
            $this->expired_date = false;
            $this->save();
        }
    }
}
