<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function StockSpecialKindDetail()
    {
        return $this->hasMany(StockSpecialKindDetail::class);
    }
    public function StockSpecialKind()
    {
        return $this->hasMany(StockSpecialKind::class);
    }
}
