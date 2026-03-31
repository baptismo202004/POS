<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSerial extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'purchase_id',
        'branch_id',
        'serial_number',
        'status',
        'warranty_expiry_date',
        'sale_item_id',
        'sold_at',
    ];

    protected $casts = [
        'warranty_expiry_date' => 'date',
        'sold_at' => 'datetime',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function saleItem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function repairs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductRepair::class);
    }
}
