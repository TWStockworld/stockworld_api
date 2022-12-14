<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCalculateGroup extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function StockCalculate()
    {
        return $this->hasMany(StockCalculate::class);
    }
    public function StockCalculateOptimal()
    {
        return $this->hasMany(StockCalculateOptimal::class);
    }
}
