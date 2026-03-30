<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'unit_type_id',
        'quantity',
        'unit_price',
        'subtotal',
        'warranty_months',
        'fulfilled_qty',
        'pending_qty',
        'available_stock_at_sale',
        'fulfillment_status',
        'is_for_procurement',
    ];

    protected $casts = [
        'quantity'               => 'decimal:2',
        'unit_price'             => 'decimal:2',
        'subtotal'               => 'decimal:2',
        'warranty_months'        => 'integer',
        'fulfilled_qty'          => 'decimal:2',
        'pending_qty'            => 'decimal:2',
        'available_stock_at_sale'=> 'decimal:2',
        'is_for_procurement'     => 'boolean',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
