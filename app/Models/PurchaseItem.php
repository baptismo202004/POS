<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'primary_quantity',
        'multiplier',
        'base_quantity',
        'quantity',
        'unit_type_id',
        'base_unit_type_id',
        'unit_cost',
        'subtotal',
    ];

    protected $casts = [
        'primary_quantity' => 'decimal:4',
        'multiplier' => 'decimal:4',
        'base_quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relationships
    public function purchase(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unitType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(UnitType::class);
    }
}
