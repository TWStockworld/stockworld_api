<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCalculate extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function StockAName()
    {
        return $this->belongsTo(StockName::class, 'stockA_name_id');
    }
    public function StockBName()
    {
        return $this->belongsTo(StockName::class, 'stockB_name_id');
    }
}
