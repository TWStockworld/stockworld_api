<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockName extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function StockCategory()
    {
        return $this->belongsTo(StockCategory::class);
    }
}
