<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInsHead extends Model
{
    protected $table = 'stock_ins_head';
    
    protected $fillable = [
        'branch_id',
        'purchase_id',
        'stock_in_date',
        'reference_number',
        'notes',
        'total_quantity',
        'status',
        'created_by',
    ];

    protected $casts = [
        'stock_in_date' => 'date',
        'total_quantity' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'source_id', 'id')
            ->where('source_type', 'stock_ins_head');
    }
}
