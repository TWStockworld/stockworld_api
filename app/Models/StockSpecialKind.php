<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockSpecialKind extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function StockSpecialKindDetail()
    {
        return $this->hasMany(StockSpecialKindDetail::class);
    }
}
