<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'barcode',
        'brand_id',
        'category_id',
        'product_type_id',
        'model_number',
        'image',
        'tracking_type',
        'warranty_type',
        'warranty_coverage_months',
        'voltage_specs',
        'status',
        'branch_id',
        'supplier_id',
        'min_stock_level',
        'low_stock_threshold',
        'max_stock_level',
        'stock_status',
    ];

    // Cast product_type_id to string since we changed it to varchar
    protected $casts = [
        'product_type_id' => 'string',
    ];

    // Relationships
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function unitTypes()
    {
        return $this->belongsToMany(UnitType::class, 'product_unit_type');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'product_branch');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function serials()
    {
        return $this->hasMany(ProductSerial::class);
    }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getCurrentStockAttribute()
    {
        $stockIn = $this->stockIns()->sum('quantity');
        $sold = $this->stockIns()->sum('sold');

        return $stockIn - $sold;
    }

    public function getCurrentBranchIdAttribute()
    {
        // This will be set when loading from out-of-stock page
        return $this->attributes['current_branch_id'] ?? null;
    }

    public function getTotalSoldAttribute()
    {
        return $this->saleItems()->sum('quantity');
    }

    public function getTotalRevenueAttribute()
    {
        return $this->saleItems()->sum('subtotal');
    }

    public function getStockAtBranch($branchId)
    {
        $stockIn = $this->stockIns()->where('branch_id', $branchId)->sum('quantity');
        $sold = $this->stockIns()->where('branch_id', $branchId)->sum('sold');

        return $stockIn - $sold;
    }
}
