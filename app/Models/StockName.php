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
    public function StockSpecialKindDetail()
    {
        return $this->hasMany(StockSpecialKindDetail::class);
    }
    public static function get_stock_name_id($name)
    {
        $result = StockName::where('stock_id',$name)->first()->id;
    
        return $result;
    }
    public static function get_stock_name($stock_id)
    {
        $result = StockName::where('stock_id',$stock_id)->first()->stock_name;
    
        return $result;
    }
}
