@extends('layouts.app', ['title' => $product->name . ' | AURA Clothing'])

@section('content')
<div class="bg-white" x-data="productViewer(@js($variantsData), {{ $product->base_price }})">

    {{-- Breadcrumb --}}
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center gap-2 text-[11px] text-gray-400 uppercase tracking-wider">
            <a href="{{ route('shop.home') }}" class="hover:text-black transition">Home</a>
            <span>/</span>
            <a href="{{ route('shop.catalog') }}" class="hover:text-black transition">Shop</a>
            @if($product->category)
                <span>/</span>
                <a href="{{ route('shop.catalog', ['category' => $product->category->slug]) }}" class="hover:text-black transition">{{ $product->category->name }}</a>
            @endif
            <span>/</span>
            <span class="text-black font-semibold truncate max-w-[200px]">{{ $product->name }}</span>
        </nav>
    </div>

    {{-- Product Main --}}
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16">

            {{-- ========================= --}}
            {{-- LEFT: Product Images --}}
            {{-- ========================= --}}
            <div class="space-y-3">
                {{-- Main Image --}}
                <div class="relative overflow-hidden bg-gray-100" style="aspect-ratio: 3/4;">
                    <img :src="currentImage || '{{ $product->image }}'"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover transition-opacity duration-300">

                    {{-- Out of Stock Badge (Only when sold out) --}}
                    <template x-if="!isCurrentVariantInStock()">
                        <div class="absolute top-4 left-4 bg-black/90 text-white text-[9px] font-bold uppercase tracking-widest px-3 py-1.5">
                            Sold Out
                        </div>
                    </template>

                    {{-- NEW Badge --}}
                    <div class="absolute top-4 right-4 bg-black text-white text-[9px] font-bold uppercase tracking-wider px-3 py-1.5">
                        NEW
                    </div>
                </div>

                {{-- Thumbnail strip (same image as fallback) --}}
                <div class="grid grid-cols-4 gap-2">
                    @for($t = 0; $t < 4; $t++)
                        <div class="overflow-hidden bg-gray-100 border-2 {{ $t === 0 ? 'border-black' : 'border-transparent hover:border-gray-300' }} cursor-pointer transition" style="aspect-ratio: 1;">
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </div>
                    @endfor
                </div>

                {{-- Value Props --}}
                <div class="grid grid-cols-3 gap-2 pt-2">
                    <div class="text-center py-4 border border-gray-200 bg-gray-50">
                        <svg class="w-5 h-5 mx-auto mb-1.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-black">Free Shipping</p>
                        <p class="text-[9px] text-gray-500 mt-0.5">Orders over Rs. 3000</p>
                    </div>
                    <div class="text-center py-4 border border-gray-200 bg-gray-50">
                        <svg class="w-5 h-5 mx-auto mb-1.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-black">Easy Returns</p>
                        <p class="text-[9px] text-gray-500 mt-0.5">30-day policy</p>
                    </div>
                    <div class="text-center py-4 border border-gray-200 bg-gray-50">
                        <svg class="w-5 h-5 mx-auto mb-1.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-black">100% Authentic</p>
                        <p class="text-[9px] text-gray-500 mt-0.5">Premium fabrics</p>
                    </div>
                </div>
            </div>

            {{-- ========================= --}}
            {{-- RIGHT: Product Details --}}
            {{-- ========================= --}}
            <div class="lg:pt-4 space-y-6">

                {{-- Brand & SKU --}}
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-[0.35em] text-gray-400">{{ $product->brand ?: 'AURA' }}</span>
                    <span class="text-[10px] text-gray-400 uppercase tracking-wider" x-text="currentVariant ? 'SKU: ' + currentVariant.sku : 'SKU: —'"></span>
                </div>

                {{-- Product Name --}}
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-black leading-tight">
                    {{ $product->name }}
                </h1>

                {{-- Price --}}
                <div class="flex items-baseline gap-3 border-y border-gray-100 py-4">
                    <span class="text-3xl font-bold text-black" x-text="currentPrice">{{ $product->price_display }}</span>
                    <span class="text-xs text-gray-400">Inclusive of all taxes</span>
                </div>

                {{-- Description --}}
                <div class="text-sm text-gray-600 leading-relaxed">
                    {{ $product->description ?: 'Crafted with premium quality fabric and attention to detail, this piece is designed to elevate your everyday wardrobe with contemporary style.' }}
                </div>

                {{-- ========================= --}}
                {{-- DYNAMIC VARIANT SELECTORS --}}
                {{-- ========================= --}}
                <div class="space-y-6">
                    @foreach($product->available_attributes as $attr)
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[11px] font-bold uppercase tracking-widest text-black">
                                    {{ $attr['name'] }}:
                                    <span class="text-gray-500 font-normal ml-1" x-text="selectedOptions['{{ $attr['name'] }}'] || 'Select'"></span>
                                </span>
                                @if(strtolower($attr['name']) === 'size')
                                    <button type="button" class="text-[10px] text-gray-500 hover:text-black underline uppercase tracking-wider transition">Size Guide</button>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-2">
                                @foreach($attr['values'] as $val)
                                    @if(!empty($val['color_code']))
                                        {{-- Color Swatch --}}
                                        <button type="button"
                                                @click="selectOption('{{ $attr['name'] }}', '{{ $val['value'] }}')"
                                                :class="selectedOptions['{{ $attr['name'] }}'] === '{{ $val['value'] }}' ? 'ring-2 ring-black ring-offset-2 scale-110' : 'hover:scale-105'"
                                                class="w-9 h-9 rounded-full border border-gray-300 transition-all flex items-center justify-center relative shadow-sm"
                                                style="background-color: {{ $val['color_code'] }}"
                                                title="{{ $val['value'] }}">
                                            <template x-if="selectedOptions['{{ $attr['name'] }}'] === '{{ $val['value'] }}'">
                                                <svg class="w-4 h-4 text-white drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </template>
                                        </button>
                                    @else
                                        {{-- Size / Text Button --}}
                                        <button type="button"
                                                @click="selectOption('{{ $attr['name'] }}', '{{ $val['value'] }}')"
                                                :class="selectedOptions['{{ $attr['name'] }}'] === '{{ $val['value'] }}' ? 'bg-black text-white border-black' : 'bg-white text-gray-700 border-gray-300 hover:border-black hover:text-black'"
                                                class="min-w-[44px] px-4 py-2.5 text-xs font-semibold border transition uppercase tracking-wider">
                                            {{ $val['value'] }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ADD TO BAG --}}
                <form action="{{ route('shop.cart.add') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="variant_id" :value="currentVariant ? currentVariant.id : ''">

                    <div class="flex gap-3">
                        {{-- Quantity --}}
                        <div class="flex items-center border border-gray-300 bg-white" style="min-width: 120px;">
                            <button type="button" @click="if(quantity > 1) quantity--"
                                    class="w-10 h-12 text-gray-600 hover:text-black flex items-center justify-center text-lg font-light transition">−</button>
                            <input type="number" name="quantity" x-model="quantity" min="1" :max="currentStock"
                                   class="flex-grow w-10 bg-transparent text-center text-sm font-semibold text-black focus:outline-none">
                            <button type="button" @click="if(quantity < currentStock) quantity++"
                                    class="w-10 h-12 text-gray-600 hover:text-black flex items-center justify-center text-lg font-light transition">+</button>
                        </div>

                        {{-- Add to Bag Button --}}
                        <button type="submit"
                                :disabled="!currentVariant || !isCurrentVariantInStock()"
                                :class="isCurrentVariantInStock() ? 'bg-black hover:bg-gray-900 text-white cursor-pointer' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                                class="flex-grow h-12 text-xs font-bold uppercase tracking-widest transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span x-text="isCurrentVariantInStock() ? 'Add to Bag' : 'Sold Out'"></span>
                        </button>
                    </div>

                    {{-- Variant warning --}}
                    <template x-if="!currentVariant">
                        <p class="text-[11px] text-amber-600 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Please select all options above
                        </p>
                    </template>
                </form>

                {{-- Accordions --}}
                <div class="space-y-0 border-t border-gray-200 pt-6" x-data="{ openTab: null }">

                    {{-- Tab: Details --}}
                    <div class="border-b border-gray-200">
                        <button type="button" @click="openTab = (openTab === 1 ? null : 1)"
                                class="w-full flex items-center justify-between py-4 text-xs font-bold uppercase tracking-widest text-black text-left">
                            <span>Product Details</span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="openTab === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openTab === 1" x-cloak class="pb-4 text-xs text-gray-600 leading-relaxed space-y-1.5">
                            <p>• Premium quality fabric construction for lasting durability</p>
                            <p>• Tailored fit with contemporary silhouette</p>
                            <p>• Double-stitched seams for reinforced strength</p>
                            <p>• Machine washable — cold water, inside-out</p>
                        </div>
                    </div>

                    {{-- Tab: Shipping --}}
                    <div class="border-b border-gray-200">
                        <button type="button" @click="openTab = (openTab === 2 ? null : 2)"
                                class="w-full flex items-center justify-between py-4 text-xs font-bold uppercase tracking-widest text-black text-left">
                            <span>Shipping & Returns</span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="openTab === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openTab === 2" x-cloak class="pb-4 text-xs text-gray-600 leading-relaxed space-y-1.5">
                            <p>• Free delivery on orders above Rs. 3,000</p>
                            <p>• Standard delivery: 3–5 business days across Pakistan</p>
                            <p>• Express delivery available at checkout</p>
                            <p>• 30-day easy return policy — no questions asked</p>
                        </div>
                    </div>

                    {{-- Tab: Size Guide --}}
                    <div class="border-b border-gray-200">
                        <button type="button" @click="openTab = (openTab === 3 ? null : 3)"
                                class="w-full flex items-center justify-between py-4 text-xs font-bold uppercase tracking-widest text-black text-left">
                            <span>Size Guide</span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="openTab === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openTab === 3" x-cloak class="pb-4">
                            <table class="w-full text-[11px] text-gray-600 border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left py-2 px-3 font-bold uppercase tracking-wider border-b border-gray-200">Size</th>
                                        <th class="text-left py-2 px-3 font-bold uppercase tracking-wider border-b border-gray-200">Chest</th>
                                        <th class="text-left py-2 px-3 font-bold uppercase tracking-wider border-b border-gray-200">Waist</th>
                                        <th class="text-left py-2 px-3 font-bold uppercase tracking-wider border-b border-gray-200">Length</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr><td class="py-2 px-3">S</td><td class="py-2 px-3">36"</td><td class="py-2 px-3">30"</td><td class="py-2 px-3">27"</td></tr>
                                    <tr><td class="py-2 px-3">M</td><td class="py-2 px-3">38"</td><td class="py-2 px-3">32"</td><td class="py-2 px-3">28"</td></tr>
                                    <tr><td class="py-2 px-3">L</td><td class="py-2 px-3">40"</td><td class="py-2 px-3">34"</td><td class="py-2 px-3">29"</td></tr>
                                    <tr><td class="py-2 px-3">XL</td><td class="py-2 px-3">42"</td><td class="py-2 px-3">36"</td><td class="py-2 px-3">30"</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- RELATED PRODUCTS --}}
    {{-- ========================= --}}
    @if($relatedProducts->isNotEmpty())
        <div class="bg-gray-50 py-14 mt-8">
            <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                <h3 class="font-serif text-2xl sm:text-3xl font-bold text-black mb-8">You May Also Like</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5">
                    @foreach($relatedProducts as $rel)
                        <div class="group flex flex-col">
                            <div class="relative overflow-hidden bg-gray-100" style="aspect-ratio: 3/4;">
                                <a href="{{ route('shop.product', $rel->slug) }}" class="block w-full h-full">
                                    <img src="{{ $rel->image }}" alt="{{ $rel->name }}"
                                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                </a>
                                <div class="absolute bottom-0 left-0 right-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                    <a href="{{ route('shop.product', $rel->slug) }}"
                                       class="block bg-black text-white text-center text-[11px] font-bold uppercase tracking-widest py-3 hover:bg-gray-900 transition">
                                        Shop Now
                                    </a>
                                </div>
                            </div>
                            <div class="pt-3">
                                <span class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">{{ $rel->brand ?: 'AURA' }}</span>
                                <a href="{{ route('shop.product', $rel->slug) }}"
                                   class="block text-sm font-semibold text-gray-900 hover:text-black line-clamp-1 mt-0.5">{{ $rel->name }}</a>
                                <span class="text-sm font-bold text-black mt-1 block">{{ $rel->price_display }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function productViewer(variants, basePrice) {
    return {
        variants: variants || [],
        basePrice: basePrice,
        selectedOptions: {},
        currentVariant: null,
        currentPrice: 'Rs. ' + parseFloat(basePrice).toFixed(0),
        currentStock: 0,
        currentImage: '',
        quantity: 1,

        init() {
            if (this.variants.length > 0) {
                let first = this.variants[0];
                for (let key in first.attributes) {
                    this.selectedOptions[key] = first.attributes[key];
                }
                this.syncVariant();
            }
        },

        selectOption(attrName, attrValue) {
            this.selectedOptions[attrName] = attrValue;
            this.syncVariant();
        },

        syncVariant() {
            let match = this.variants.find(v => {
                for (let key in this.selectedOptions) {
                    if (v.attributes[key] !== this.selectedOptions[key]) return false;
                }
                return true;
            });

            if (match) {
                this.currentVariant = match;
                this.currentPrice = match.price_formatted;
                this.currentStock = match.stock;
                if (match.image) this.currentImage = match.image;
                if (this.quantity > match.stock) this.quantity = match.stock > 0 ? 1 : 0;
            } else {
                this.currentVariant = null;
                this.currentStock = 0;
            }
        },

        isCurrentVariantInStock() {
            return this.currentVariant && this.currentVariant.stock > 0 && this.currentVariant.is_active;
        }
    };
}
</script>
@endpush
@endsection
