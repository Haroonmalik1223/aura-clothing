<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function home()
    {
        $categories = Category::active()->withCount('products')->get();

        // All featured products (shown on initial "All" tab)
        $featuredProducts = Product::active()
            ->with(['category', 'variants.attributeValues.attribute'])
            ->latest()
            ->take(10)
            ->get();

        // Products grouped by category for the tab switcher
        $productsByCategory = [];
        foreach ($categories as $cat) {
            $productsByCategory[$cat->slug] = Product::active()
                ->where('category_id', $cat->id)
                ->with(['category', 'variants.attributeValues.attribute'])
                ->latest()
                ->get();
        }

        return view('shop.home', compact('categories', 'featuredProducts', 'productsByCategory'));
    }

    public function collections()
    {
        $categories = Category::active()->withCount('products')->get();
        return view('shop.collections', compact('categories'));
    }

    public function catalog(Request $request)
    {
        $query = Product::active()->with(['category', 'variants.attributeValues.attribute']);

        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->get('sort') === 'price_low') {
            $query->orderBy('base_price', 'asc');
        } elseif ($request->get('sort') === 'price_high') {
            $query->orderBy('base_price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->withCount('products')->get();
        $activeCategory = $request->category ? Category::where('slug', $request->category)->first() : null;

        return view('shop.catalog', compact('products', 'categories', 'activeCategory'));
    }

    public function show($slug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['category', 'variants.attributeValues.attribute'])
            ->firstOrFail();

        // Prepare JSON payload for the frontend dynamic variant switcher
        $variantsData = $product->variants->map(function ($v) {
            $attrMap = [];
            foreach ($v->attributeValues as $val) {
                if ($val->attribute) {
                    $attrMap[$val->attribute->name] = $val->value;
                }
            }
            return [
                'id' => $v->id,
                'sku' => $v->sku,
                'price' => (float)$v->price,
                'price_formatted' => 'Rs. ' . number_format($v->price, 0),
                'stock' => $v->stock_quantity,
                'image' => $v->image ?: $product->image,
                'is_active' => (bool)$v->is_active,
                'attributes' => $attrMap,
            ];
        });

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['variants'])
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'variantsData', 'relatedProducts'));
    }

    // Cart actions
    public function cart()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('shop.cart', compact('cart', 'total'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $variant = ProductVariant::with(['product', 'attributeValues.attribute'])->findOrFail($request->variant_id);
        $quantity = $request->quantity ?: 1;

        if ($variant->stock_quantity < $quantity) {
            return back()->with('error', 'Sorry, selected variant is out of stock.');
        }

        $cart = session()->get('cart', []);
        $id = $variant->id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'id' => $variant->id,
                'product_id' => $variant->product_id,
                'product_name' => $variant->product->name,
                'product_slug' => $variant->product->slug,
                'brand' => $variant->product->brand,
                'variant_title' => $variant->variant_title,
                'sku' => $variant->sku,
                'price' => (float)$variant->price,
                'image' => $variant->image ?: $variant->product->image,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Added to your shopping bag.');
    }

    public function removeFromCart(Request $request)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session()->put('cart', $cart);
        }
        return back()->with('success', 'Item removed from bag.');
    }

    public function clearCart()
    {
        session()->forget('cart');
        return back()->with('success', 'Cart cleared.');
    }
}
