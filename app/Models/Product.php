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
        'selling_price',
        'purchase_price',
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

    /**
     * Get the display label for the product type.
     *
     * This prefers the category's `category_type` (from the categories table) when available,
     * and falls back to legacy `product_type_id` / related product type records.
     */
    public function getDisplayProductTypeAttribute(): string
    {
        $type = null;

        if (! empty($this->category?->category_type)) {
            $type = $this->category->category_type;
        } elseif (! empty($this->product_type_id)) {
            $type = $this->product_type_id;
        } elseif (! empty($this->productType?->type_name)) {
            $type = $this->productType->type_name;
        }

        $type = strtolower(trim((string) $type));

        if (in_array($type, ['non_electronic', 'non-electronic', 'nonelectronic'], true)) {
            return 'Non-Electronic';
        }

        if ($type === 'electronic_with_serial') {
            return 'Electronic (with serial)';
        }

        if ($type === 'electronic_without_serial') {
            return 'Electronic (without serial)';
        }

        if ($type === 'electronic') {
            return 'Electronic';
        }

        if ($type === '') {
            return 'N/A';
        }

        return ucwords(str_replace(['_', '-'], ' ', $type));
    }

    public function getDisplayProductTypeBadgeClassAttribute(): string
    {
        $type = strtolower(trim((string) ($this->category?->category_type ?? $this->product_type_id ?? '')));

        if (in_array($type, ['non_electronic', 'non-electronic', 'nonelectronic'], true)) {
            return 'amber';
        }

        if (strpos($type, 'electronic') === 0) {
            return 'blue';
        }

        return 'blue';
    }

    public function unitTypes()
    {
        return $this->belongsToMany(UnitType::class, 'product_unit_type')
            ->withPivot(['conversion_factor', 'is_base'])
            ->withTimestamps();
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'product_branch')->withTimestamps();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function serials()
    {
        return $this->hasMany(ProductSerial::class);
    }

    public function repairs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductRepair::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function branchStocks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BranchStock::class);
    }

    public function getCurrentStockAttribute()
    {
        return $this->branchStocks()->sum('quantity_base');
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
        return (float) ($this->branchStocks()->where('branch_id', $branchId)->value('quantity_base') ?? 0);
    }
}
