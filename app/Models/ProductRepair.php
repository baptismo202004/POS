<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRepair extends Model
{
    protected $fillable = [
        'product_id',
        'product_serial_id',
        'sale_item_id',
        'branch_id',
        'handled_by',
        'serial_number',
        'repair_type',
        'status',
        'issue_description',
        'resolution_notes',
        'repair_cost',
        'received_date',
        'returned_date',
    ];

    protected $casts = [
        'repair_cost' => 'decimal:2',
        'received_date' => 'date',
        'returned_date' => 'date',
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productSerial(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductSerial::class);
    }

    public function saleItem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function handledBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
