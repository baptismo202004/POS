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
        'unit_type_id',
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

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }

    public function serials()
    {
        return $this->hasMany(ProductSerial::class);
    }
}
