@extends('layouts.admin', ['title' => 'Add Product (Dynamic Variants) | AURA Admin'])

@section('content')
<div class="space-y-6" x-data="productVariantBuilder()">
    
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold font-display tracking-tight text-slate-900 flex items-center space-x-2">
                <span>Add Clothing Product</span>
                <span class="text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 px-2.5 py-0.5 rounded-full font-mono font-semibold">
                    Dynamic Variant Flow
                </span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Configure apparel product info, dynamic attributes (Size, Color, Fit, etc.), and generate SKU variants dynamically.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs text-slate-500 hover:text-slate-900 flex items-center space-x-1 font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back to Products</span>
        </a>
    </div>

    <!-- Main Form -->
    <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-8" @submit="validateBeforeSubmit($event)">
        @csrf

        <!-- STEP 1: Basic Product Information -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-6 shadow-sm">
            <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">1. Product Essentials</h2>
                    <p class="text-[11px] text-slate-500">Core brand styling, category classification, and baseline pricing.</p>
                </div>
                <span class="text-xs font-mono font-semibold text-slate-400">Step 1 of 3</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Product Name -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Product Title *</label>
                    <input type="text" name="name" x-model="productName" @input="updateSkuSuggestions()" required placeholder="e.g. Heavyweight 500GSM Boxy Zip Hoodie" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900">
                </div>

                <!-- Category -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Category *</label>
                        <a href="{{ route('admin.categories.create') }}" target="_blank" class="text-[11px] text-indigo-600 hover:underline font-semibold">+ New Category</a>
                    </div>
                    <select name="category_id" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-slate-900">
                        <option value="">Select Category...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Brand -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Brand / Line</label>
                    <input type="text" name="brand" value="{{ old('brand', 'AURA STUDIO') }}" placeholder="e.g. AURA STUDIO" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:border-slate-900">
                </div>

                <!-- Base Price -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Base Price ($ USD) *</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-slate-400 font-mono text-sm">$</span>
                        <input type="number" step="0.01" min="0" name="base_price" x-model="basePrice" @input="updateDefaultPrices()" required placeholder="68.00" class="w-full bg-slate-50 border border-slate-300 rounded-lg pl-8 pr-3.5 py-2.5 text-sm font-mono text-slate-900 focus:outline-none focus:border-slate-900">
                    </div>
                </div>

                <!-- Default Stock helper -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Default Variant Stock</label>
                    <input type="number" min="0" x-model="defaultStock" @input="updateDefaultStock()" placeholder="20" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm font-mono text-slate-900 focus:outline-none focus:border-slate-900">
                </div>

                <!-- Image URL -->
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Main Product Image URL</label>
                    <div class="flex gap-2">
                        <input type="url" name="image" x-model="productImage" placeholder="https://images.unsplash.com/photo-..." class="flex-grow bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:border-slate-900">
                    </div>
                    <!-- Sample Presets for Quick Testing -->
                    <div class="flex flex-wrap items-center gap-2 mt-2 text-[11px] text-slate-500">
                        <span class="font-medium text-slate-600">Quick fashion presets:</span>
                        <button type="button" @click="productImage = 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=800&q=80'" class="text-indigo-600 hover:underline">Hoodie</button>
                        <span>&bull;</span>
                        <button type="button" @click="productImage = 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80'" class="text-indigo-600 hover:underline">T-Shirt</button>
                        <span>&bull;</span>
                        <button type="button" @click="productImage = 'https://images.unsplash.com/photo-1542272604-780c96856592?auto=format&fit=crop&w=800&q=80'" class="text-indigo-600 hover:underline">Denim</button>
                        <span>&bull;</span>
                        <button type="button" @click="productImage = 'https://images.unsplash.com/photo-1544441893-675973e31985?auto=format&fit=crop&w=800&q=80'" class="text-indigo-600 hover:underline">Jacket</button>
                        <span>&bull;</span>
                        <button type="button" @click="productImage = 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?auto=format&fit=crop&w=800&q=80'" class="text-indigo-600 hover:underline">Trousers</button>
                    </div>
                </div>

                <!-- Description -->
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Description</label>
                    <textarea name="description" rows="3" placeholder="Crafted with high-gauge combed yarn, oversized silhouette, double-needle coverstitch..." class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:border-slate-900"></textarea>
                </div>
            </div>

            <!-- Toggles -->
            <div class="flex flex-wrap items-center gap-6 pt-3 border-t border-slate-200">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-0">
                    <span class="text-xs text-slate-700 font-semibold">Publish Active on Storefront</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" checked class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-0">
                    <span class="text-xs text-slate-700 font-semibold">Show in Featured Capsule Section</span>
                </label>
            </div>
        </div>

        <!-- STEP 2: Dynamic Attribute Definition (NOT statically defined!) -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-6 shadow-sm">
            <div class="border-b border-slate-200 pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <div class="flex items-center space-x-2">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">2. Dynamic Clothing Attributes</h2>
                        <span class="text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full font-mono font-bold">
                            Non-Static / Dynamic Format
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Add any attribute (Size, Color, Material, Waist, Fit, Sleeve, etc.) and its possible options.</p>
                </div>
                
                <!-- Quick Preset Dropdown/Pills -->
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <span class="text-slate-500 text-[11px] font-medium">Quick Presets:</span>
                    <button type="button" @click="addAttributePreset('Size', ['S', 'M', 'L', 'XL'])" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-1 rounded-md text-[11px] border border-slate-200 font-medium transition">
                        + Size (S, M, L, XL)
                    </button>
                    <button type="button" @click="addAttributePreset('Color', ['Washed Black', 'Vintage Heather Grey', 'Sage Olive'])" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-1 rounded-md text-[11px] border border-slate-200 font-medium transition">
                        + Colors (3)
                    </button>
                    <button type="button" @click="addAttributePreset('Waist Size', ['30', '32', '34', '36'])" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-1 rounded-md text-[11px] border border-slate-200 font-medium transition">
                        + Waist (30-36)
                    </button>
                </div>
            </div>

            <!-- Dynamic Attribute Rows -->
            <div class="space-y-4">
                <template x-for="(attr, attrIndex) in attributes" :key="attrIndex">
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 space-y-3 relative group">
                        
                        <div class="flex items-center justify-between gap-4">
                            <!-- Attribute Name -->
                            <div class="w-1/3">
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Attribute Name
                                </label>
                                <input type="text" x-model="attr.name" placeholder="e.g. Size, Color, Fit, Fabric" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 font-medium">
                            </div>

                            <!-- Attribute Values (Comma-separated) -->
                            <div class="w-2/3">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                        Options / Values (comma separated)
                                    </label>
                                    <span class="text-[10px] text-slate-500 font-mono" x-text="parseValues(attr.valuesText).length + ' option(s)'"></span>
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" x-model="attr.valuesText" placeholder="e.g. Small, Medium, Large OR Washed Black, Sandstone, Olive" class="flex-grow bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900">
                                    
                                    <!-- Delete Attribute Button -->
                                    <button type="button" @click="removeAttribute(attrIndex)" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Remove attribute">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Render parsed badges preview -->
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            <template x-for="val in parseValues(attr.valuesText)" :key="val">
                                <span class="inline-flex items-center text-[11px] bg-white text-slate-700 border border-slate-200 px-2 py-0.5 rounded font-mono shadow-xs">
                                    <span x-text="val"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Add Dynamic Attribute Button -->
                <div class="flex items-center justify-between pt-2">
                    <button type="button" @click="addAttribute()" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-indigo-700 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-3.5 py-2 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>+ Add Another Dynamic Attribute</span>
                    </button>

                    <!-- Generate Variants Action -->
                    <button type="button" @click="generateVariantMatrix()" class="inline-flex items-center space-x-2 text-xs font-bold uppercase tracking-wider bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg transition shadow-md">
                        <span>⚡ Generate Variant Matrix</span>
                        <span class="bg-white/20 font-mono text-[10px] px-1.5 py-0.5 rounded" x-text="calculatePossibleVariantsCount() + ' combos'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- STEP 3: Variant Matrix & Inventory Details -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-6 shadow-sm">
            <div class="border-b border-slate-200 pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-2">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">3. Generated Variant Matrix</h2>
                        <span class="text-xs font-mono font-bold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200" x-text="variants.length + ' Active Variants'"></span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Customize individual SKUs, pricing variations, and inventory stock per dynamic variant.</p>
                </div>

                <!-- Bulk Quick Adjust Tool -->
                <div class="flex flex-wrap items-center gap-2 bg-slate-50 border border-slate-200 p-2 rounded-lg text-xs">
                    <span class="text-slate-500 text-[10px] font-mono font-bold">BULK APPLY:</span>
                    <div class="flex items-center space-x-1">
                        <input type="number" step="0.01" x-model="bulkPrice" placeholder="Price" class="w-20 bg-white border border-slate-300 text-xs px-2 py-1 rounded text-slate-900 font-mono">
                        <button type="button" @click="applyBulkPrice()" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-2 py-1 rounded text-[10px] font-semibold transition">Set Prices</button>
                    </div>
                    <div class="flex items-center space-x-1">
                        <input type="number" min="0" x-model="bulkStock" placeholder="Stock" class="w-16 bg-white border border-slate-300 text-xs px-2 py-1 rounded text-slate-900 font-mono">
                        <button type="button" @click="applyBulkStock()" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-2 py-1 rounded text-[10px] font-semibold transition">Set Stock</button>
                    </div>
                </div>
            </div>

            <!-- Variant Rows Table -->
            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase tracking-wider font-semibold">
                            <th class="py-3 px-3 w-10 text-center">#</th>
                            <th class="py-3 px-3">Variant Combination</th>
                            <th class="py-3 px-3">Unique SKU *</th>
                            <th class="py-3 px-3 w-32">Price ($ USD) *</th>
                            <th class="py-3 px-3 w-28">Stock Quantity *</th>
                            <th class="py-3 px-3 text-center w-16">Remove</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <template x-for="(variant, vIndex) in variants" :key="vIndex">
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-2.5 px-3 text-center font-mono text-slate-400" x-text="vIndex + 1"></td>
                                
                                <!-- Variant Attributes Badge -->
                                <td class="py-2.5 px-3">
                                    <div class="flex flex-wrap gap-1.5 items-center">
                                        <template x-for="(attrVal, attrKey) in variant.attributes" :key="attrKey">
                                            <span class="inline-flex items-center text-[11px] bg-slate-100 text-slate-700 border border-slate-200 px-2 py-0.5 rounded">
                                                <strong class="text-slate-500 mr-1" x-text="attrKey + ':'"></strong>
                                                <span class="font-bold text-slate-900" x-text="attrVal"></span>
                                            </span>
                                        </template>
                                        <template x-if="Object.keys(variant.attributes).length === 0">
                                            <span class="text-slate-400 italic text-[11px]">Standard / Default Variant</span>
                                        </template>

                                        <!-- Hidden Inputs to bind attributes to the controller submission -->
                                        <template x-for="(attrVal, attrKey) in variant.attributes" :key="attrKey">
                                            <input type="hidden" :name="'variants[' + vIndex + '][attributes][' + attrKey + ']'" :value="attrVal">
                                        </template>
                                    </div>
                                </td>

                                <!-- SKU -->
                                <td class="py-2.5 px-3">
                                    <input type="text" :name="'variants[' + vIndex + '][sku]'" x-model="variant.sku" required class="w-full bg-slate-50 border border-slate-300 rounded px-2.5 py-1.5 text-xs font-mono font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                                </td>

                                <!-- Price -->
                                <td class="py-2.5 px-3">
                                    <div class="relative">
                                        <span class="absolute left-2.5 top-1.5 text-slate-400 font-mono text-xs">$</span>
                                        <input type="number" step="0.01" min="0" :name="'variants[' + vIndex + '][price]'" x-model="variant.price" required class="w-full bg-slate-50 border border-slate-300 rounded pl-6 pr-2 py-1.5 text-xs font-mono font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                                    </div>
                                </td>

                                <!-- Stock Quantity -->
                                <td class="py-2.5 px-3">
                                    <input type="number" min="0" :name="'variants[' + vIndex + '][stock_quantity]'" x-model="variant.stock_quantity" required class="w-full bg-slate-50 border border-slate-300 rounded px-2.5 py-1.5 text-xs font-mono font-semibold text-slate-900 focus:outline-none focus:border-slate-900">
                                </td>

                                <!-- Delete Row -->
                                <td class="py-2.5 px-3 text-center">
                                    <button type="button" @click="removeVariant(vIndex)" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded transition" title="Remove variant combo">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State -->
                        <template x-if="variants.length === 0">
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    <p class="text-xs">No variants configured yet.</p>
                                    <p class="text-[11px] text-slate-500 mt-1">Add dynamic attributes above and click "Generate Variant Matrix", or add a custom variant.</p>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Manual Custom Variant Adder -->
            <div class="flex items-center justify-between pt-1">
                <button type="button" @click="addManualVariant()" class="text-xs text-slate-600 hover:text-slate-900 font-semibold flex items-center space-x-1 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>+ Add Manual Single Variant</span>
                </button>
                <span class="text-xs text-slate-500 font-mono">
                    Total Inventory: <strong class="text-slate-900" x-text="totalInventory()"></strong> units
                </span>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center justify-between shadow-sm">
            <a href="{{ route('admin.products.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">Cancel & Return</a>
            
            <button type="submit" class="inline-flex items-center space-x-2 bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Publish Clothing Product & Variants</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function productVariantBuilder() {
    return {
        productName: '',
        basePrice: 65.00,
        defaultStock: 25,
        bulkPrice: 65.00,
        bulkStock: 25,
        productImage: 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=800&q=80',

        // Dynamic Attributes: initially populated with typical clothing attributes for convenience
        attributes: [
            { name: 'Size', valuesText: 'S, M, L, XL' },
            { name: 'Color', valuesText: 'Washed Black, Vintage Heather Grey, Sage Olive' }
        ],

        // Variants Matrix list
        variants: [],

        init() {
            // Auto generate initial variants based on default clothing attributes
            this.generateVariantMatrix();
        },

        addAttribute() {
            this.attributes.push({ name: '', valuesText: '' });
        },

        addAttributePreset(name, values) {
            let existing = this.attributes.find(a => a.name.toLowerCase() === name.toLowerCase());
            if (existing) {
                existing.valuesText = values.join(', ');
            } else {
                this.attributes.push({ name: name, valuesText: values.join(', ') });
            }
            this.generateVariantMatrix();
        },

        removeAttribute(index) {
            this.attributes.splice(index, 1);
        },

        parseValues(str) {
            if (!str) return [];
            return str.split(',').map(s => s.trim()).filter(s => s.length > 0);
        },

        calculatePossibleVariantsCount() {
            let active = this.attributes.filter(a => a.name.trim() !== '' && this.parseValues(a.valuesText).length > 0);
            if (active.length === 0) return 0;
            return active.reduce((acc, a) => acc * this.parseValues(a.valuesText).length, 1);
        },

        generateVariantMatrix() {
            let activeAttrs = this.attributes.filter(a => a.name.trim() !== '' && this.parseValues(a.valuesText).length > 0);
            
            if (activeAttrs.length === 0) {
                this.variants = [{
                    sku: this.buildSku({}),
                    price: this.basePrice || 0,
                    stock_quantity: this.defaultStock || 0,
                    attributes: {}
                }];
                return;
            }

            // Cartesian product algorithm
            let matrix = [{}];
            for (let attr of activeAttrs) {
                let values = this.parseValues(attr.valuesText);
                let newMatrix = [];
                for (let existing of matrix) {
                    for (let val of values) {
                        let copy = Object.assign({}, existing);
                        copy[attr.name.trim()] = val;
                        newMatrix.push(copy);
                    }
                }
                matrix = newMatrix;
            }

            this.variants = matrix.map((combo, idx) => {
                let sku = this.buildSku(combo, idx + 1);
                return {
                    sku: sku,
                    price: this.basePrice || 50,
                    stock_quantity: this.defaultStock || 20,
                    attributes: combo
                };
            });
        },

        buildSku(combo, index = 1) {
            let prefix = 'AURA';
            if (this.productName) {
                let words = this.productName.toUpperCase().replace(/[^A-Z0-9\s]/g, '').split(/\s+/).filter(Boolean);
                if (words.length > 0) {
                    prefix = words.slice(0, 2).map(w => w.substring(0, 3)).join('');
                }
            }

            let parts = [prefix];
            for (let key in combo) {
                let val = combo[key].toUpperCase().replace(/[^A-Z0-9]/g, '');
                parts.push(val.substring(0, 3));
            }
            parts.push(String(index).padStart(2, '0'));
            return parts.join('-');
        },

        updateSkuSuggestions() {
            this.variants.forEach((v, idx) => {
                v.sku = this.buildSku(v.attributes, idx + 1);
            });
        },

        updateDefaultPrices() {
            if (this.basePrice) {
                this.bulkPrice = this.basePrice;
            }
        },

        updateDefaultStock() {
            if (this.defaultStock) {
                this.bulkStock = this.defaultStock;
            }
        },

        applyBulkPrice() {
            if (this.bulkPrice === '' || isNaN(this.bulkPrice)) return;
            this.variants.forEach(v => v.price = parseFloat(this.bulkPrice).toFixed(2));
        },

        applyBulkStock() {
            if (this.bulkStock === '' || isNaN(this.bulkStock)) return;
            this.variants.forEach(v => v.stock_quantity = parseInt(this.bulkStock));
        },

        removeVariant(index) {
            this.variants.splice(index, 1);
        },

        addManualVariant() {
            this.variants.push({
                sku: this.buildSku({}, this.variants.length + 1),
                price: this.basePrice || 50,
                stock_quantity: this.defaultStock || 10,
                attributes: {}
            });
        },

        totalInventory() {
            return this.variants.reduce((sum, v) => sum + (parseInt(v.stock_quantity) || 0), 0);
        },

        validateBeforeSubmit(e) {
            if (this.variants.length === 0) {
                alert('Please generate or add at least one variant before submitting.');
                e.preventDefault();
                return;
            }
            let skus = this.variants.map(v => v.sku.trim().toUpperCase());
            let unique = new Set(skus);
            if (unique.size !== skus.length) {
                alert('Each variant must have a unique SKU. Please check your SKU fields.');
                e.preventDefault();
                return;
            }
        }
    };
}
</script>
@endpush
@endsection
