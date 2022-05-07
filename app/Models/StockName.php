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
    public function StockData()
    {
        return $this->hasMany(StockData::class);
    }
    public static function get_virtual_stock_id($name)
    {
        $result = StockName::where('stock_id',$name)->first()->id;
    
        return $result;
    }
}
