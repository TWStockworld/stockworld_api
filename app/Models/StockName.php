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
    public function StockCalculateStockA()
    {
        return $this->hasMany(StockCalculate::class, 'stockA_name_id');
    }
    public function StockCalculateStockB()
    {
        return $this->hasMany(StockCalculate::class, 'stockB_name_id');
    }
    public static function get_stock_name_id($stock_id)
    {
        $result = StockName::where('stock_id', $stock_id)->first()->id;

        return $result;
    }
    public static function get_stock_name($stock_id)
    {
        $result = StockName::where('stock_id', $stock_id)->first()->stock_name;

        return $result;
    }
    public static function get_stock_name_useid($id)
    {
        $result = StockName::find($id)->stock_name;

        return $result;
    }
    public static function get_stock_id($id)
    {
        $result = StockName::find($id)->stock_id;

        return $result;
    }
}
