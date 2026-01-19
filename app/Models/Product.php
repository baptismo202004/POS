<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\SaleItem;

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
        $stockOut = $this->stockOuts()->sum('quantity');
        return $stockIn - $stockOut;
    }
}
