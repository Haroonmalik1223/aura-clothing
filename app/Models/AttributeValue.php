<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value',
        'color_code',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_values');
    }

    /**
     * Accessor: dynamically resolve color hex code if attribute is Color and not manually set.
     */
    public function getColorCodeAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        $attrName = strtolower($this->attribute->name ?? '');
        if (strpos($attrName, 'color') !== false || strpos($attrName, 'colour') !== false) {
            return self::resolveColorCode($this->value);
        }

        return null;
    }

    /**
     * Map common fashion color names or hex strings to CSS hex codes.
     */
    public static function resolveColorCode(?string $colorName): ?string
    {
        if (empty($colorName)) return null;

        $name = trim($colorName);

        // If direct HEX code provided (e.g. #ff0000 or #fff)
        if (preg_match('/^#([a-f0-9]{6}|[a-f0-9]{3})$/i', $name)) {
            return strtolower($name);
        }

        $normalized = strtolower($name);

        $palette = [
            'black' => '#18181b',
            'washed black' => '#27272a',
            'jet black' => '#09090b',
            'charcoal' => '#374151',
            'anthracite' => '#1f2937',
            
            'white' => '#ffffff',
            'chalk white' => '#f8fafc',
            'off white' => '#f1f5f9',
            'cream' => '#fefce8',
            'ivory' => '#fafaf9',
            'bone' => '#e7e5e4',
            
            'grey' => '#9ca3af',
            'gray' => '#9ca3af',
            'vintage heather grey' => '#9ca3af',
            'heather grey' => '#9ca3af',
            'light grey' => '#d1d5db',
            'dark grey' => '#4b5563',
            'slate' => '#64748b',
            
            'navy' => '#0f172a',
            'navy blue' => '#1e293b',
            'blue' => '#2563eb',
            'royal blue' => '#1d4ed8',
            'indigo' => '#312e81',
            'raw indigo' => '#1e3a8a',
            'sky blue' => '#38bdf8',
            'light blue' => '#7dd3fc',
            'baby blue' => '#93c5fd',
            'ice blue' => '#bae6fd',
            'teal' => '#0f766e',
            'cyan' => '#06b6d4',
            
            'green' => '#16a34a',
            'forest green' => '#14532d',
            'emerald' => '#047857',
            'pine green' => '#064e3b',
            'olive' => '#4d7c0f',
            'sage olive' => '#3f6212',
            'army green' => '#365314',
            'sage' => '#84a98c',
            'mint' => '#86efac',
            'lime' => '#65a30d',
            'khaki' => '#a3a375',
            'military khaki' => '#71717a',
            
            'beige' => '#d4b996',
            'sand' => '#d6c7b2',
            'sandstone' => '#c2b29f',
            'tan' => '#d2b48c',
            'camel' => '#c19a6b',
            'brown' => '#78350f',
            'earth brown' => '#5c2d12',
            'chocolate' => '#451a03',
            'coffee' => '#3e2723',
            'mocha' => '#4e342e',
            
            'rust' => '#9a3412',
            'terracotta' => '#9a3412',
            'terracotta rust' => '#9a3412',
            'copper' => '#b45309',
            'brick' => '#7f1d1d',
            
            'red' => '#dc2626',
            'crimson' => '#991b1b',
            'ruby' => '#be123c',
            'maroon' => '#831843',
            'burgundy' => '#701a75',
            'wine' => '#4a044e',
            
            'pink' => '#ec4899',
            'baby pink' => '#fbcfe8',
            'rose' => '#f43f5e',
            'dusty rose' => '#fda4af',
            'blush' => '#fecdd3',
            'magenta' => '#d946ef',
            'fuchsia' => '#c026d3',
            
            'purple' => '#9333ea',
            'violet' => '#7c3aed',
            'lavender' => '#c084fc',
            'lilac' => '#d8b4fe',
            'plum' => '#581c87',
            
            'yellow' => '#eab308',
            'mustard' => '#ca8a04',
            'mustard yellow' => '#ca8a04',
            'gold' => '#d97706',
            
            'orange' => '#ea580c',
            'coral' => '#f87171',
            'peach' => '#fdba74',
            'amber' => '#d97706',
        ];

        if (isset($palette[$normalized])) {
            return $palette[$normalized];
        }

        // Keyword lookup inside compound strings (e.g. "Forest Green Plaid" -> green)
        foreach ($palette as $key => $hex) {
            if (strpos($normalized, $key) !== false) {
                return $hex;
            }
        }

        return null;
    }
}
