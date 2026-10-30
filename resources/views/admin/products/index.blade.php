@extends('layouts.admin', ['title' => 'Products & Variants | AURA Admin'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-display tracking-tight text-slate-900">Apparel Products & Variants</h1>
            <p class="text-xs text-slate-500 mt-1">Manage clothing catalog, multi-attribute variants, inventory, and pricing.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.categories.index') }}" class="bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 px-3.5 py-2 rounded-lg text-xs font-semibold transition shadow-sm">
                Manage Categories
            </a>
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center space-x-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>+ Add Product (Dynamic Flow)</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
        <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <!-- Search -->
            <div class="relative flex-grow sm:flex-grow-0 sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, brand, slug..." class="w-full bg-slate-50 border border-slate-300 rounded-lg pl-9 pr-3 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Category Filter -->
            <select name="category" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-xs text-slate-700 focus:outline-none focus:border-slate-900">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-xs px-3.5 py-2 rounded-lg font-semibold text-slate-700 border border-slate-200 transition">Filter</button>
            @if(request('search') || request('category'))
                <a href="{{ route('admin.products.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline">Clear</a>
            @endif
        </form>

        <div class="text-xs text-slate-500 font-mono">
            Total Products: <span class="font-bold text-slate-900">{{ $products->total() }}</span>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase tracking-wider font-semibold">
                        <th class="py-3.5 px-4">Product Details</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Price Range</th>
                        <th class="py-3.5 px-4">Dynamic Variants Breakdown</th>
                        <th class="py-3.5 px-4 text-center">Total Stock</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Product Details -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3.5">
                                    <div class="w-12 h-14 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0">
                                        @if($product->image)
                                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400 font-bold">A</div>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-[10px] tracking-widest uppercase font-mono text-indigo-600 font-semibold block">{{ $product->brand ?: 'AURA' }}</span>
                                        <a href="{{ route('shop.product', $product->slug) }}" target="_blank" class="font-bold text-slate-900 text-sm hover:text-indigo-600 transition block">
                                            {{ $product->name }}
                                        </a>
                                        <span class="text-[11px] text-slate-400 font-mono">/product/{{ $product->slug }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-3.5 px-4">
                                @if($product->category)
                                    <span class="inline-block bg-slate-100 border border-slate-200 text-slate-700 px-2 py-0.5 rounded text-[11px] font-medium">
                                        {{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Uncategorized</span>
                                @endif
                            </td>

                            <!-- Price Range -->
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                {{ $product->price_display }}
                            </td>

                            <!-- Dynamic Variants Breakdown -->
                            <td class="py-3.5 px-4">
                                <div class="space-y-1.5 max-w-sm">
                                    <div class="flex items-center space-x-2">
                                        <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 font-mono font-bold text-[10px] px-2 py-0.5 rounded">
                                            {{ $product->variants->count() }} Variants
                                        </span>
                                    </div>
                                    
                                    <!-- List attributes dynamically -->
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($product->available_attributes as $attr)
                                            <span class="inline-flex items-center text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200">
                                                <strong class="text-slate-800 mr-1">{{ $attr['name'] }}:</strong>
                                                {{ count($attr['values']) }} options
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </td>

                            <!-- Total Stock -->
                            <td class="py-3.5 px-4 text-center">
                                @php $stock = $product->total_stock; @endphp
                                @if($stock > 20)
                                    <span class="font-mono text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                        {{ $stock }} in stock
                                    </span>
                                @elseif($stock > 0)
                                    <span class="font-mono text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                                        {{ $stock }} low stock
                                    </span>
                                @else
                                    <span class="font-mono text-xs font-bold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-200">
                                        Out of stock
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 text-center">
                                @if($product->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-500">Draft</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <a href="{{ route('shop.product', $product->slug) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition" title="Preview on Store">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="p-1.5 text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition" title="Edit Product & Variants">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product and all its variants?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <p class="text-sm">No products found matching your criteria.</p>
                                <a href="{{ route('admin.products.create') }}" class="inline-block mt-3 text-indigo-600 hover:underline text-xs font-semibold">+ Add your first product with dynamic variants</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
