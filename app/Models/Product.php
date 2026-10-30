<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'brand',
        'description',
        'base_price',
        'image',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getMinPriceAttribute()
    {
        if ($this->variants && $this->variants->isNotEmpty()) {
            return $this->variants->min('price');
        }
        return $this->base_price;
    }

    public function getMaxPriceAttribute()
    {
        if ($this->variants && $this->variants->isNotEmpty()) {
            return $this->variants->max('price');
        }
        return $this->base_price;
    }

    public function getPriceDisplayAttribute()
    {
        return 'Rs. ' . number_format($this->base_price, 0);
    }

    public function getTotalStockAttribute()
    {
        if ($this->variants && $this->variants->isNotEmpty()) {
            return $this->variants->sum('stock_quantity');
        }
        return 0;
    }

    /**
     * Get distinct attributes and their available values for this product
     */
    public function getAvailableAttributesAttribute()
    {
        $attributes = [];
        $this->loadMissing(['variants.attributeValues.attribute']);

        foreach ($this->variants as $variant) {
            if (!$variant->is_active) {
                continue;
            }
            foreach ($variant->attributeValues as $val) {
                $attr = $val->attribute;
                if (!$attr) continue;

                if (!isset($attributes[$attr->id])) {
                    $attributes[$attr->id] = [
                        'id' => $attr->id,
                        'name' => $attr->name,
                        'slug' => $attr->slug,
                        'values' => [],
                    ];
                }

                if (!isset($attributes[$attr->id]['values'][$val->id])) {
                    $attributes[$attr->id]['values'][$val->id] = [
                        'id' => $val->id,
                        'value' => $val->value,
                        'color_code' => $val->color_code,
                    ];
                }
            }
        }

        // Convert associative values to indexed arrays
        return array_map(function ($attr) {
            $attr['values'] = array_values($attr['values']);
            return $attr;
        }, array_values($attributes));
    }
}
