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
    public static function get_stock_category_id($name)
    {
        $result = StockCategory::where('category', $name)->first()->id;

        return $result;
    }
    public static function get_stock_category_name($id)
    {
        $result = StockCategory::find($id)->category;

        return $result;
    }
}
