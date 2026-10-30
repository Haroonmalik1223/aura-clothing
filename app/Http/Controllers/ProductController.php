<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants.attributeValues.attribute']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(12);
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        // Existing attributes to offer quick auto-completion
        $existingAttributes = Attribute::with('values')->get();

        return view('admin.products.create', compact('categories', 'existingAttributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand' => 'nullable|string|max:100',
            'base_price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:1000',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'required|string|max:100|distinct|unique:product_variants,sku',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock_quantity' => 'required|integer|min:0',
        ], [
            'variants.required' => 'Please generate or add at least one product variant.',
            'variants.*.sku.distinct' => 'Each variant must have a unique SKU.',
            'variants.*.sku.unique' => 'One of the variant SKUs is already taken in the database.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Product
            $slug = Str::slug($request->name);
            $origSlug = $slug;
            $count = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $origSlug . '-' . $count++;
            }

            $product = Product::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => $slug,
                'brand' => $request->brand ?: 'AURA APPAREL',
                'base_price' => $request->base_price,
                'image' => $request->image ?: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80',
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
            ]);

            // Cache attributes & values in memory to minimize SQL queries
            $attrCache = [];
            $valCache = [];

            // 2. Loop through variants
            foreach ($request->variants as $vData) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => trim($vData['sku']),
                    'price' => $vData['price'],
                    'stock_quantity' => $vData['stock_quantity'],
                    'image' => !empty($vData['image']) ? $vData['image'] : $product->image,
                    'is_active' => true,
                ]);

                // 3. Attach dynamic attributes if present
                if (!empty($vData['attributes']) && is_array($vData['attributes'])) {
                    $attributeValueIds = [];
                    foreach ($vData['attributes'] as $attrName => $attrValue) {
                        $attrName = trim($attrName);
                        $attrValue = trim($attrValue);
                        if ($attrName === '' || $attrValue === '') continue;

                        $attrKey = strtolower($attrName);
                        if (!isset($attrCache[$attrKey])) {
                            $attrObj = Attribute::firstOrCreate(
                                ['slug' => Str::slug($attrName)],
                                ['name' => $attrName]
                            );
                            $attrCache[$attrKey] = $attrObj;
                        } else {
                            $attrObj = $attrCache[$attrKey];
                        }

                        $valKey = $attrObj->id . ':' . strtolower($attrValue);
                        if (!isset($valCache[$valKey])) {
                            $valObj = AttributeValue::firstOrCreate(
                                ['attribute_id' => $attrObj->id, 'value' => $attrValue]
                            );
                            $valCache[$valKey] = $valObj;
                        } else {
                            $valObj = $valCache[$valKey];
                        }

                        $attributeValueIds[] = $valObj->id;
                    }

                    if (!empty($attributeValueIds)) {
                        $variant->attributeValues()->sync($attributeValueIds);
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', "Product '{$product->name}' with " . count($request->variants) . " variant(s) was created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating product: ' . $e->getMessage());
        }
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'variants.attributeValues.attribute']);
        $categories = Category::active()->orderBy('name')->get();

        $attributesMap = [];
        $initialVariants = $product->variants->map(function ($v) use (&$attributesMap) {
            $attrs = [];
            foreach ($v->attributeValues as $val) {
                if ($val->attribute) {
                    $attrName = $val->attribute->name;
                    $attrs[$attrName] = $val->value;
                    $attributesMap[$attrName][] = $val->value;
                }
            }
            return [
                'id' => $v->id,
                'sku' => $v->sku,
                'price' => (float) $v->price,
                'stock_quantity' => (int) $v->stock_quantity,
                'attributes' => (object) $attrs,
            ];
        })->values();

        $initialAttributes = [];
        foreach ($attributesMap as $name => $values) {
            $initialAttributes[] = [
                'name' => $name,
                'valuesText' => implode(', ', array_unique($values)),
            ];
        }

        if (empty($initialAttributes)) {
            $initialAttributes = [
                ['name' => 'Size', 'valuesText' => 'S, M, L, XL'],
            ];
        }

        return view('admin.products.edit', compact('product', 'categories', 'initialAttributes', 'initialVariants'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand' => 'nullable|string|max:100',
            'base_price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:1000',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'required|string|max:100|distinct',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock_quantity' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $product->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'brand' => $request->brand ?: 'AURA APPAREL',
                'base_price' => $request->base_price,
                'image' => $request->image ?: $product->image,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
            ]);

            // Update existing or add new variants
            $submittedVariantIds = [];
            $attrCache = [];
            $valCache = [];

            foreach ($request->variants as $vData) {
                if (!empty($vData['id']) && $variant = ProductVariant::where('product_id', $product->id)->find($vData['id'])) {
                    // Update existing
                    $variant->update([
                        'sku' => trim($vData['sku']),
                        'price' => $vData['price'],
                        'stock_quantity' => $vData['stock_quantity'],
                    ]);
                } else {
                    // Create new
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => trim($vData['sku']),
                        'price' => $vData['price'],
                        'stock_quantity' => $vData['stock_quantity'],
                        'image' => $product->image,
                        'is_active' => true,
                    ]);
                }
                $submittedVariantIds[] = $variant->id;

                if (!empty($vData['attributes']) && is_array($vData['attributes'])) {
                    $attributeValueIds = [];
                    foreach ($vData['attributes'] as $attrName => $attrValue) {
                        $attrName = trim($attrName);
                        $attrValue = trim($attrValue);
                        if ($attrName === '' || $attrValue === '') continue;

                        $attrKey = strtolower($attrName);
                        if (!isset($attrCache[$attrKey])) {
                            $attrObj = Attribute::firstOrCreate(
                                ['slug' => Str::slug($attrName)],
                                ['name' => $attrName]
                            );
                            $attrCache[$attrKey] = $attrObj;
                        } else {
                            $attrObj = $attrCache[$attrKey];
                        }

                        $valKey = $attrObj->id . ':' . strtolower($attrValue);
                        if (!isset($valCache[$valKey])) {
                            $valObj = AttributeValue::firstOrCreate(
                                ['attribute_id' => $attrObj->id, 'value' => $attrValue]
                            );
                            $valCache[$valKey] = $valObj;
                        } else {
                            $valObj = $valCache[$valKey];
                        }

                        $attributeValueIds[] = $valObj->id;
                    }
                    $variant->attributeValues()->sync($attributeValueIds);
                }
            }

            // Remove any variants deleted by user during edit
            ProductVariant::where('product_id', $product->id)
                ->whereNotIn('id', $submittedVariantIds)
                ->delete();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', "Product '{$product->name}' updated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Product '{$name}' was deleted.");
    }
}
