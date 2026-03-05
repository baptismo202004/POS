<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInUnitPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_in_id',
        'unit_type_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function stockIn(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(StockIn::class);
    }

    public function unitType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(UnitType::class);
    }
}
