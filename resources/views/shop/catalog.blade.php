@extends('layouts.app', ['title' => ($activeCategory ? $activeCategory->name . ' | ' : '') . 'Shop | AURA Clothing'])

@section('content')
<div class="bg-white min-h-screen">

    {{-- Page Header --}}
    <div class="bg-gray-50 border-b border-gray-200 py-10">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-[11px] text-gray-400 uppercase tracking-wider mb-3">
                <a href="{{ route('shop.home') }}" class="hover:text-black transition">Home</a>
                <span>/</span>
                @if($activeCategory)
                    <a href="{{ route('shop.collections') }}" class="hover:text-black transition">Collections</a>
                    <span>/</span>
                    <span class="text-black font-semibold">{{ $activeCategory->name }}</span>
                @else
                    <span class="text-black font-semibold">Complete Apparel Collection</span>
                @endif
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h1 class="font-serif text-3xl sm:text-4xl font-bold text-black">
                        {{ $activeCategory ? $activeCategory->name : 'Complete Apparel Collection' }}
                    </h1>
                    @if($activeCategory && $activeCategory->description)
                        <p class="text-sm text-gray-500 mt-1 max-w-2xl leading-relaxed">{{ $activeCategory->description }}</p>
                    @endif
                </div>
                <span class="text-xs text-gray-400 font-medium whitespace-nowrap">{{ $products->total() }} {{ Str::plural('Product', $products->total()) }}</span>
            </div>
        </div>
    </div>

    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- If viewing general catalog (no category), show category switcher. If category is selected, do NOT show the all-categories filter --}}
        @if(!$activeCategory)
            <div class="flex items-center gap-2 overflow-x-auto pb-3 scrollbar-hide mb-6">
                @foreach($categories as $cat)
                    <a href="{{ route('shop.catalog', ['category' => $cat->slug]) }}"
                       class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 text-[11px] font-semibold uppercase tracking-wider transition border bg-white text-gray-700 border-gray-300 hover:border-black hover:text-black">
                        {{ $cat->name }}
                        <span class="opacity-60 text-[10px]">({{ $cat->products_count }})</span>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Filters Toolbar --}}
        <div class="flex flex-wrap items-center justify-between gap-3 py-4 border-t border-b border-gray-200 mb-8">
            <form action="{{ route('shop.catalog') }}" method="GET" class="flex flex-wrap items-center gap-3">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search in this collection..."
                       class="border border-gray-300 px-4 py-2 text-xs focus:outline-none focus:border-black w-48 sm:w-56">
                <select name="sort" onchange="this.form.submit()"
                        class="border border-gray-300 px-3 py-2 text-xs text-gray-700 focus:outline-none focus:border-black bg-white">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
                <button type="submit" class="bg-black text-white px-5 py-2 text-xs font-semibold uppercase tracking-wider hover:bg-gray-800 transition">Apply</button>
                @if(request('search') || request('sort'))
                    <a href="{{ route('shop.catalog', ['category' => request('category')]) }}" class="text-xs text-gray-500 hover:text-black underline">Reset</a>
                @endif
            </form>

            @if($activeCategory)
                <a href="{{ route('shop.collections') }}" class="text-xs text-gray-500 hover:text-black uppercase tracking-wider font-medium flex items-center gap-1">
                    <span>&larr; All Collections</span>
                </a>
            @endif
        </div>

        {{-- Products Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
            @forelse($products as $product)
                @include('shop._product_card', ['product' => $product])
            @empty
                <div class="col-span-full py-24 text-center">
                    <svg class="w-14 h-14 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-base font-semibold text-gray-800 mb-1">No products found</p>
                    <p class="text-sm text-gray-500">Try adjusting your filters or search term.</p>
                    <a href="{{ route('shop.catalog') }}" class="inline-block mt-4 border border-black text-black px-8 py-2.5 text-xs font-bold uppercase tracking-wider hover:bg-black hover:text-white transition">
                        Clear Filters
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
