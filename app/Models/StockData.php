<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class StockData extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function StockName()
    {
        return $this->belongsTo(StockName::class);
    }
    public static function check_stock_data_ifexists($stock_virtual_id, $date)
    {   
        if(StockData::where(['stock_name_id' => $stock_virtual_id, 'date' => $date])->cursor()->count()!=0){
            return true;
        }
        else{
            return false;
        }
        // return StockData::where(['stock_name_id' => $stock_virtual_id, 'date' => $date])->exists();
    }
}
