<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock_quantity',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_values')
                    ->withTimestamps();
    }

    /**
     * Get a human readable label of this variant, e.g. "Size: M / Color: Black"
     */
    public function getVariantTitleAttribute()
    {
        if ($this->relationLoaded('attributeValues')) {
            return $this->attributeValues->map(function ($val) {
                return ($val->attribute ? $val->attribute->name : 'Option') . ': ' . $val->value;
            })->implode(' / ');
        }
        return $this->sku;
    }
}
