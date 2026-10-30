<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariant;

class ClothingStoreSeeder extends Seeder
{
    public function run()
    {
        // 1. Categories
        $categoriesData = [
            [
                'name' => 'Hoodies & Sweatshirts',
                'slug' => 'hoodies-sweatshirts',
                'description' => 'Heavyweight drop-shoulder hoodies, premium French terry crewnecks, and street-ready outerwear.',
                'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Oversized & Graphic Tees',
                'slug' => 'tees-and-tops',
                'description' => '240 GSM organic cotton tees featuring relaxed silhouettes and minimalist brand detailing.',
                'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Trousers & Cargo Pants',
                'slug' => 'trousers-and-cargos',
                'description' => 'Wide-leg tailored trousers, tactical pocket cargos, and relaxed utility pants.',
                'image' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Denim & Jeans',
                'slug' => 'denim-and-jeans',
                'description' => 'Japanese raw selvedge, vintage washes, and relaxed straight-leg silhouettes.',
                'image' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Jackets & Outerwear',
                'slug' => 'jackets-and-outerwear',
                'description' => 'Technical bombers, wool trench coats, and modular oversized puffer jackets.',
                'image' => 'https://images.unsplash.com/photo-1544441893-675973e31985?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 2. Attributes and Attribute Values (Dynamic)
        $attributesData = [
            'Size' => [
                ['value' => 'XS', 'color_code' => null],
                ['value' => 'S', 'color_code' => null],
                ['value' => 'M', 'color_code' => null],
                ['value' => 'L', 'color_code' => null],
                ['value' => 'XL', 'color_code' => null],
            ],
            'Color' => [
                ['value' => 'Washed Black', 'color_code' => '#18181b'],
                ['value' => 'Vintage Heather Grey', 'color_code' => '#9ca3af'],
                ['value' => 'Chalk White', 'color_code' => '#f8fafc'],
                ['value' => 'Sage Olive', 'color_code' => '#4d7c0f'],
                ['value' => 'Terracotta Rust', 'color_code' => '#9a3412'],
                ['value' => 'Raw Indigo', 'color_code' => '#1e3a8a'],
                ['value' => 'Military Khaki', 'color_code' => '#71717a'],
            ],
            'Waist Size' => [
                ['value' => '30', 'color_code' => null],
                ['value' => '32', 'color_code' => null],
                ['value' => '34', 'color_code' => null],
                ['value' => '36', 'color_code' => null],
            ],
            'Fit' => [
                ['value' => 'Relaxed Oversized', 'color_code' => null],
                ['value' => 'Regular Fit', 'color_code' => null],
                ['value' => 'Straight Cut', 'color_code' => null],
            ],
        ];

        $valMap = [];
        foreach ($attributesData as $attrName => $values) {
            $attr = Attribute::firstOrCreate(
                ['slug' => Str::slug($attrName)],
                ['name' => $attrName, 'slug' => Str::slug($attrName)]
            );

            foreach ($values as $valData) {
                $val = AttributeValue::firstOrCreate(
                    ['attribute_id' => $attr->id, 'value' => $valData['value']],
                    ['color_code' => $valData['color_code']]
                );
                $valMap[$attrName . ':' . $valData['value']] = $val;
            }
        }

        // 3. Products with Dynamic Variants - Each product has a DISTINCT, UNIQUE image!
        $productsSeed = [
            // ==================== 1. HOODIES & SWEATSHIRTS ====================
            [
                'category_slug' => 'hoodies-sweatshirts',
                'name' => 'Heavyweight 460GSM French Terry Hoodie',
                'slug' => 'heavyweight-460gsm-french-terry-hoodie',
                'brand' => 'AURA STUDIO',
                'base_price' => 5490.00,
                'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=800&q=80',
                'description' => 'Crafted from custom-developed 460 GSM organic French terry cotton. Features double-layered hood without drawstrings, seamless kangaroo pocket, and double-needle stitching throughout for lifetime durability.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L', 'XL'],
                    'Color' => ['Washed Black', 'Vintage Heather Grey'],
                ],
                'sku_prefix' => 'AURA-HD1',
            ],
            [
                'category_slug' => 'hoodies-sweatshirts',
                'name' => 'Vintage Washed Drop-Shoulder Hoodie',
                'slug' => 'vintage-washed-drop-shoulder-hoodie',
                'brand' => 'AURA ATELIER',
                'base_price' => 5890.00,
                'image' => 'https://images.unsplash.com/photo-1509942774463-acf339cf87d5?auto=format&fit=crop&w=800&q=80',
                'description' => 'Garment-dyed and enzyme-washed for a naturally worn-in patina. Generous drop-shoulder cut with ribbed cuffs and hem for modern streetwear proportion.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['M', 'L', 'XL'],
                    'Color' => ['Terracotta Rust', 'Washed Black'],
                ],
                'sku_prefix' => 'AURA-HD2',
            ],
            [
                'category_slug' => 'hoodies-sweatshirts',
                'name' => 'Minimalist Essential Zip-Up Hoodie',
                'slug' => 'minimalist-essential-zip-up-hoodie',
                'brand' => 'AURA LABS',
                'base_price' => 6490.00,
                'image' => 'https://images.unsplash.com/photo-1578587018452-892bacefd3f2?auto=format&fit=crop&w=800&q=80',
                'description' => 'Full-zip front with heavy-gauge matte two-way YKK hardware. Constructed from 400 GSM loopback cotton fleece with clean finished interior seams.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L'],
                    'Color' => ['Washed Black', 'Sage Olive'],
                ],
                'sku_prefix' => 'AURA-HD3',
            ],
            [
                'category_slug' => 'hoodies-sweatshirts',
                'name' => 'Relaxed Heavy Knit Crewneck Sweatshirt',
                'slug' => 'relaxed-heavy-knit-crewneck-sweatshirt',
                'brand' => 'AURA STUDIO',
                'base_price' => 4990.00,
                'image' => 'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?auto=format&fit=crop&w=800&q=80',
                'description' => 'Clean architectural crewneck cut from brushed heavy fleece. Features reinforced collar ribbing and wide ergonomic sleeve openings for effortless daily layering.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L', 'XL'],
                    'Color' => ['Vintage Heather Grey', 'Chalk White'],
                ],
                'sku_prefix' => 'AURA-CRW',
            ],

            // ==================== 2. OVERSIZED & GRAPHIC TEES ====================
            [
                'category_slug' => 'tees-and-tops',
                'name' => 'Boxy Fit Heavy Jersey T-Shirt',
                'slug' => 'boxy-fit-heavy-jersey-t-shirt',
                'brand' => 'AURA STUDIO',
                'base_price' => 2490.00,
                'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80',
                'description' => 'Essential heavyweight 260 GSM combed cotton tee. Cut in our signature boxy silhouette with dropped shoulders and a high-rib collar that will never bacon.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['XS', 'S', 'M', 'L', 'XL'],
                    'Color' => ['Chalk White', 'Washed Black'],
                ],
                'sku_prefix' => 'AURA-TEE1',
            ],
            [
                'category_slug' => 'tees-and-tops',
                'name' => 'Acid Wash Vintage Graphic T-Shirt',
                'slug' => 'acid-wash-vintage-graphic-t-shirt',
                'brand' => 'AURA ATELIER',
                'base_price' => 2890.00,
                'image' => 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?auto=format&fit=crop&w=800&q=80',
                'description' => 'Custom acid-washed 240 GSM jersey featuring tonal puff print typography on the chest and back. Pre-shrunk and silicone-washed for ultra-soft hand feel.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L', 'XL'],
                    'Color' => ['Washed Black', 'Vintage Heather Grey'],
                ],
                'sku_prefix' => 'AURA-TEE2',
            ],
            [
                'category_slug' => 'tees-and-tops',
                'name' => 'Raw Hem Streetwear Relaxed Tee',
                'slug' => 'raw-hem-streetwear-relaxed-tee',
                'brand' => 'AURA LABS',
                'base_price' => 2690.00,
                'image' => 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?auto=format&fit=crop&w=800&q=80',
                'description' => 'Deconstructed silhouette with subtle raw hem edge detailing and blind-stitched sleeves. Woven from 100% Peruvian pima cotton.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L'],
                    'Color' => ['Washed Black', 'Sage Olive'],
                ],
                'sku_prefix' => 'AURA-TEE3',
            ],
            [
                'category_slug' => 'tees-and-tops',
                'name' => 'Heavy Combed Cotton Pocket Tee',
                'slug' => 'heavy-combed-cotton-pocket-tee',
                'brand' => 'AURA STUDIO',
                'base_price' => 2390.00,
                'image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=800&q=80',
                'description' => 'A workwear-inspired daily staple featuring a reinforced chest patch pocket, bar-tack stitching, and thick 1.25-inch rib knit neckline.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L', 'XL'],
                    'Color' => ['Chalk White', 'Sage Olive'],
                ],
                'sku_prefix' => 'AURA-TEE4',
            ],

            // ==================== 3. TROUSERS & CARGO PANTS ====================
            [
                'category_slug' => 'trousers-and-cargos',
                'name' => 'Pleated Wide-Leg Tailored Trouser',
                'slug' => 'pleated-wide-leg-tailored-trouser',
                'brand' => 'AURA ATELIER',
                'base_price' => 5290.00,
                'image' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?auto=format&fit=crop&w=800&q=80',
                'description' => 'Relaxed double-pleated trouser crafted from an Italian wool-blend twill with an elegant drape. Finished with horn buttons and an internal waistband curtain.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L'],
                    'Color' => ['Washed Black', 'Vintage Heather Grey'],
                ],
                'sku_prefix' => 'AURA-TRS1',
            ],
            [
                'category_slug' => 'trousers-and-cargos',
                'name' => 'Relaxed Italian Wool Twill Trouser',
                'slug' => 'relaxed-italian-wool-twill-trouser',
                'brand' => 'AURA ATELIER',
                'base_price' => 5990.00,
                'image' => 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?auto=format&fit=crop&w=800&q=80',
                'description' => 'Tailored from lightweight tropical wool woven in Biella, Italy. Features forward side pockets, welted back pockets, and an unhemmed finish for bespoke tailoring.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L'],
                    'Color' => ['Military Khaki', 'Washed Black'],
                ],
                'sku_prefix' => 'AURA-TRS2',
            ],
            [
                'category_slug' => 'trousers-and-cargos',
                'name' => 'Tactical Multi-Pocket Cargo Pants',
                'slug' => 'tactical-multi-pocket-cargo-pants',
                'brand' => 'AURA LABS',
                'base_price' => 5490.00,
                'image' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=800&q=80',
                'description' => 'Heavy cotton-ripstop utility pants with articulated knees, bellows cargo pockets with magnetic closures, and adjustable bungee toggles at the ankle cuffs.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L', 'XL'],
                    'Color' => ['Sage Olive', 'Washed Black'],
                ],
                'sku_prefix' => 'AURA-CRG',
            ],
            [
                'category_slug' => 'trousers-and-cargos',
                'name' => 'Structured Straight-Leg Casual Chino',
                'slug' => 'structured-straight-leg-casual-chino',
                'brand' => 'AURA STUDIO',
                'base_price' => 4490.00,
                'image' => 'https://images.unsplash.com/photo-1506629082955-511b1aa562c8?auto=format&fit=crop&w=800&q=80',
                'description' => 'Dense 320 GSM combed cotton twill chino with clean front styling. Pre-washed with natural pumice stones for broken-in drape right out of the box.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L'],
                    'Color' => ['Military Khaki', 'Washed Black'],
                ],
                'sku_prefix' => 'AURA-CHN',
            ],

            // ==================== 4. DENIM & JEANS ====================
            [
                'category_slug' => 'denim-and-jeans',
                'name' => '14oz Kurabo Selvedge Relaxed Denim',
                'slug' => '14oz-kurabo-selvedge-relaxed-denim',
                'brand' => 'AURA ATELIER',
                'base_price' => 6990.00,
                'image' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=800&q=80',
                'description' => 'Woven on vintage shuttle looms in Kojima, Japan. 14oz red-line selvedge denim in an unwashed raw indigo finish that develops custom fades unique to your wear.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Waist Size' => ['30', '32', '34', '36'],
                    'Color' => ['Raw Indigo', 'Washed Black'],
                ],
                'sku_prefix' => 'AURA-JNS1',
            ],
            [
                'category_slug' => 'denim-and-jeans',
                'name' => 'Vintage Washed Wide-Leg Skate Denim',
                'slug' => 'vintage-washed-wide-leg-skate-denim',
                'brand' => 'AURA STUDIO',
                'base_price' => 6290.00,
                'image' => 'https://images.unsplash.com/photo-1565084888279-aca607ecce0c?auto=format&fit=crop&w=800&q=80',
                'description' => '90s skate-inspired wide cut with heavy stonewash whiskering, custom doughnut shank buttons, and reinforced pocket bags.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Waist Size' => ['30', '32', '34', '36'],
                    'Color' => ['Raw Indigo'],
                ],
                'sku_prefix' => 'AURA-JNS2',
            ],
            [
                'category_slug' => 'denim-and-jeans',
                'name' => 'Classic Straight Leg Vintage Denim',
                'slug' => 'classic-straight-leg-vintage-denim',
                'brand' => 'AURA STUDIO',
                'base_price' => 5990.00,
                'image' => 'https://images.unsplash.com/photo-1582552938357-32b906df40cb?auto=format&fit=crop&w=800&q=80',
                'description' => 'Timeless mid-rise straight silhouette in medium blue vintage wash. 100% long-staple cotton with custom copper rivets and leather brand patch.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Waist Size' => ['30', '32', '34', '36'],
                    'Color' => ['Raw Indigo', 'Washed Black'],
                ],
                'sku_prefix' => 'AURA-JNS3',
            ],

            // ==================== 5. JACKETS & OUTERWEAR ====================
            [
                'category_slug' => 'jackets-and-outerwear',
                'name' => 'Waterproof Ripstop MA-1 Bomber',
                'slug' => 'waterproof-ripstop-ma-1-bomber',
                'brand' => 'AURA LABS',
                'base_price' => 9490.00,
                'image' => 'https://images.unsplash.com/photo-1544441893-675973e31985?auto=format&fit=crop&w=800&q=80',
                'description' => 'Engineered with DWR-coated nylon ripstop, PrimaLoft thermal insulation, two-way YKK matte black zippers, and ergonomic articulated sleeve articulation.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L', 'XL'],
                    'Color' => ['Washed Black', 'Military Khaki'],
                ],
                'sku_prefix' => 'AURA-BMB1',
            ],
            [
                'category_slug' => 'jackets-and-outerwear',
                'name' => 'PrimaLoft Insulated Urban Flight Bomber',
                'slug' => 'primaloft-insulated-urban-flight-bomber',
                'brand' => 'AURA LABS',
                'base_price' => 11490.00,
                'image' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=800&q=80',
                'description' => 'Technical modern flight jacket with high-density windproof satin nylon shell, recessed rib cuffs, and utility pocket on the left sleeve.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L'],
                    'Color' => ['Washed Black'],
                ],
                'sku_prefix' => 'AURA-BMB2',
            ],
            [
                'category_slug' => 'jackets-and-outerwear',
                'name' => 'Heavy Corduroy Workwear Chore Jacket',
                'slug' => 'heavy-corduroy-workwear-chore-jacket',
                'brand' => 'AURA ATELIER',
                'base_price' => 8490.00,
                'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=800&q=80',
                'description' => '8-wale thick cotton corduroy chore coat with triple-needle flat-felled seam construction, four utility patch pockets, and tortoiseshell button closures.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L', 'XL'],
                    'Color' => ['Terracotta Rust', 'Washed Black'],
                ],
                'sku_prefix' => 'AURA-CHR',
            ],
            [
                'category_slug' => 'jackets-and-outerwear',
                'name' => 'Modular Matte Puffer Down Jacket',
                'slug' => 'modular-matte-puffer-down-jacket',
                'brand' => 'AURA LABS',
                'base_price' => 12990.00,
                'image' => 'https://images.unsplash.com/photo-1495105787522-5334e3ffa0ef?auto=format&fit=crop&w=800&q=80',
                'description' => '700-fill power responsible down fill encased in a water-repellent matte Japanese nylon membrane. Features storm flap zipper shield and detachable hood.',
                'is_featured' => true,
                'variant_combinations' => [
                    'Size' => ['S', 'M', 'L'],
                    'Color' => ['Washed Black'],
                ],
                'sku_prefix' => 'AURA-PUF',
            ],
        ];

        foreach ($productsSeed as $pData) {
            $cat = $categories[$pData['category_slug']] ?? null;
            $product = Product::firstOrCreate(
                ['slug' => $pData['slug']],
                [
                    'category_id' => $cat ? $cat->id : null,
                    'name' => $pData['name'],
                    'slug' => $pData['slug'],
                    'brand' => $pData['brand'],
                    'base_price' => $pData['base_price'],
                    'image' => $pData['image'],
                    'description' => $pData['description'],
                    'is_active' => true,
                    'is_featured' => $pData['is_featured'],
                ]
            );

            // Dynamically generate combinations
            $combos = $pData['variant_combinations'];

            // Compute cartesian product of attributes
            $matrix = [[]];
            foreach ($combos as $attrName => $values) {
                $newMatrix = [];
                foreach ($matrix as $existing) {
                    foreach ($values as $val) {
                        $newMatrix[] = array_merge($existing, [$attrName => $val]);
                    }
                }
                $matrix = $newMatrix;
            }

            foreach ($matrix as $index => $combo) {
                // Generate SKU
                $skuParts = [$pData['sku_prefix']];
                foreach ($combo as $attrName => $vVal) {
                    $skuParts[] = strtoupper(substr(str_replace(' ', '', $vVal), 0, 3));
                }
                $sku = implode('-', $skuParts) . '-' . ($index + 1);

                $priceVariation = 0;
                if (isset($combo['Size']) && $combo['Size'] === 'XL') {
                    $priceVariation += 4.00;
                }
                $variantPrice = $pData['base_price'] + $priceVariation;
                $stock = rand(8, 40);

                $variant = ProductVariant::firstOrCreate(
                    ['product_id' => $product->id, 'sku' => $sku],
                    [
                        'price' => $variantPrice,
                        'stock_quantity' => $stock,
                        'image' => $pData['image'],
                        'is_active' => true,
                    ]
                );

                // Attach pivot values
                $pivotIds = [];
                foreach ($combo as $attrName => $vVal) {
                    $key = $attrName . ':' . $vVal;
                    if (isset($valMap[$key])) {
                        $pivotIds[] = $valMap[$key]->id;
                    }
                }
                $variant->attributeValues()->syncWithoutDetaching($pivotIds);
            }
        }
    }
}
