<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockSpecialKindDetail extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function Bulletin()
    {
        return $this->belongsTo(Bulletin::class);
    }

    public function StockSpecialKind()
    {
        return $this->belongsTo(StockSpecialKind::class);
    }
    public function StockName()
    {
        return $this->belongsTo(StockName::class);
    }
}
