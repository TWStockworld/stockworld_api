<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCategory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function StockName()
    {
        return $this->hasMany(StockName::class);
    }
}
