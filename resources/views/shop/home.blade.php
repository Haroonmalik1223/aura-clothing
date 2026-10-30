@extends('layouts.app', ['title' => 'AURA | Premium Clothing Brand'])

@section('content')

{{-- ============================================================ --}}
{{-- HERO SLIDER --}}
{{-- ============================================================ --}}
<section x-data="{
    current: 0,
    slides: [
        {
            bg: 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1920&q=90',
            label: 'Winter Collection 2026',
            heading: 'Crafted For\nThe Season',
            sub: 'Premium winter wear with unparalleled warmth and style',
            cta: 'Shop Winter',
            ctaLink: '{{ route('shop.catalog') }}'
        },
        {
            bg: 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?auto=format&fit=crop&w=1920&q=90',
            label: 'New Arrivals — Just Dropped',
            heading: 'Elevated\nEssentials',
            sub: 'Minimalist cuts. Maximum impact. Explore our new arrivals.',
            cta: 'Shop New Arrivals',
            ctaLink: '{{ route('shop.catalog') }}'
        },
        {
            bg: 'https://images.unsplash.com/photo-1445205170230-053b83016050?auto=format&fit=crop&w=1920&q=90',
            label: 'Exclusive Styles',
            heading: 'Wear Your\nStory',
            sub: 'Discover premium Pakistani clothing with global style standards.',
            cta: 'Explore Collection',
            ctaLink: '{{ route('shop.catalog') }}'
        }
    ],
    autoplay: null,
    start() {
        this.autoplay = setInterval(() => { this.next(); }, 5000);
    },
    next() { this.current = (this.current + 1) % this.slides.length; },
    prev() { this.current = (this.current - 1 + this.slides.length) % this.slides.length; }
}" x-init="start()" class="relative overflow-hidden bg-black" style="height: 92vh; min-height: 520px; max-height: 800px;">

    {{-- Slides --}}
    <template x-for="(slide, i) in slides" :key="i">
        <div x-show="current === i"
             x-transition:enter="transition-opacity duration-700"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-500"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0">

            {{-- Background Image --}}
            <img :src="slide.bg" :alt="slide.label" class="w-full h-full object-cover">
            {{-- Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-black/10"></div>

            {{-- Content --}}
            <div class="absolute inset-0 flex items-center">
                <div class="max-w-[1440px] mx-auto px-6 sm:px-12 lg:px-16 w-full">
                    <div class="max-w-xl space-y-6 text-white">
                        <span class="inline-block text-[10px] font-semibold uppercase tracking-[0.35em] border border-white/40 text-white/80 px-4 py-1.5" x-text="slide.label"></span>
                        <h1 class="font-serif text-5xl sm:text-6xl lg:text-7xl font-bold leading-none whitespace-pre-line" x-text="slide.heading"></h1>
                        <p class="text-sm sm:text-base text-white/75 leading-relaxed" x-text="slide.sub"></p>
                        <div class="pt-2 flex items-center gap-4">
                            <a :href="slide.ctaLink"
                               class="inline-block bg-white text-black px-8 py-3.5 text-xs font-bold uppercase tracking-widest hover:bg-gray-100 transition" x-text="slide.cta"></a>
                            <a href="{{ route('shop.catalog') }}" class="text-white/70 hover:text-white text-xs font-semibold uppercase tracking-wider transition flex items-center gap-1.5">
                                View All
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Prev / Next Arrows --}}
    <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 border border-white/30 flex items-center justify-center text-white hover:bg-white/10 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 border border-white/30 flex items-center justify-center text-white hover:bg-white/10 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>

    {{-- Dots --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
        <template x-for="(slide, i) in slides" :key="i">
            <button @click="current = i"
                    :class="current === i ? 'w-8 bg-white' : 'w-2 bg-white/40'"
                    class="h-0.5 transition-all duration-300 rounded-full"></button>
        </template>
    </div>
</section>


{{-- ============================================================ --}}
{{-- SHOP BY CATEGORY --}}
{{-- ============================================================ --}}
<section class="py-16 bg-white">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-[0.35em] text-gray-400 mb-2">Browse</p>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-black">Shop By Category</h2>
            </div>
            <a href="{{ route('shop.catalog') }}" class="hidden sm:flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-gray-600 hover:text-black transition">
                View All
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Category Grid (portrait cards with overlay text) --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            @foreach($categories as $category)
                <a href="{{ route('shop.catalog', ['category' => $category->slug]) }}"
                   class="group relative overflow-hidden bg-gray-100 block" style="aspect-ratio: 3/4;">

                    {{-- Category Image --}}
                    @if($category->image)
                        <img src="{{ $category->image }}" alt="{{ $category->name }}"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    @else
                        {{-- Fallback gradient --}}
                        <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300"></div>
                    @endif

                    {{-- Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent group-hover:from-black/90 transition-all duration-300"></div>

                    {{-- Text --}}
                    <div class="absolute bottom-0 left-0 right-0 p-4">
                        <p class="text-[9px] text-white/60 uppercase tracking-widest font-medium mb-1">{{ $category->products_count }} Styles</p>
                        <h3 class="font-serif text-lg font-bold text-white leading-tight group-hover:text-yellow-300 transition-colors duration-300">
                            {{ $category->name }}
                        </h3>
                        <div class="mt-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span class="text-[10px] text-white/80 uppercase tracking-wider font-medium">Shop Now</span>
                            <svg class="w-3 h-3 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>


{{-- ============================================================ --}}
{{-- PROMOTIONAL BANNER --}}
{{-- ============================================================ --}}
<section class="relative bg-black overflow-hidden" style="height: 300px;">
    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=1920&q=80"
         alt="Sale Banner" class="w-full h-full object-cover opacity-40">
    <div class="absolute inset-0 flex items-center justify-center text-center text-white px-4">
        <div class="space-y-4">
            <p class="text-[10px] font-semibold uppercase tracking-[0.4em] text-yellow-400">Limited Time Offer</p>
            <h2 class="font-serif text-4xl sm:text-6xl font-bold">UP TO 50% OFF</h2>
            <p class="text-sm text-white/70">On Selected Winter Essentials — Shop Before It's Gone</p>
            <a href="{{ route('shop.catalog') }}"
               class="inline-block mt-2 border border-white text-white px-8 py-3 text-xs font-bold uppercase tracking-widest hover:bg-white hover:text-black transition">
                Shop the Sale
            </a>
        </div>
    </div>
</section>


{{-- ============================================================ --}}
{{-- PRODUCTS WITH CATEGORY TABS (NO "ALL" FILTER) --}}
{{-- ============================================================ --}}
<section class="py-16 bg-white" x-data="{ activeTab: '{{ $categories->first()->slug ?? '' }}' }">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="mb-8">
            <p class="text-[10px] font-semibold uppercase tracking-[0.35em] text-gray-400 mb-2">Curated Apparel</p>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-black mb-6">Explore Collections</h2>

            {{-- Category Tabs: ONLY individual categories, NO "All" filter mix --}}
            <div class="flex flex-wrap items-center gap-2 border-b border-gray-200 pb-0">
                @foreach($categories as $cat)
                    <button @click="activeTab = '{{ $cat->slug }}'"
                            :class="activeTab === '{{ $cat->slug }}'
                                ? 'border-b-2 border-black text-black font-bold'
                                : 'text-gray-400 hover:text-black border-b-2 border-transparent'"
                            class="px-2 pb-3 text-[11px] uppercase tracking-widest font-semibold transition whitespace-nowrap">
                        {{ $cat->name }}
                        <span class="ml-1 text-[9px] opacity-60">({{ $cat->products_count }})</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Per-Category Panels --}}
        @foreach($categories as $cat)
            <div x-show="activeTab === '{{ $cat->slug }}'" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                @if(isset($productsByCategory[$cat->slug]) && $productsByCategory[$cat->slug]->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
                        @foreach($productsByCategory[$cat->slug] as $product)
                            @include('shop._product_card', ['product' => $product])
                        @endforeach
                    </div>

                    {{-- Link to category page --}}
                    <div class="mt-8 text-center">
                        <a href="{{ route('shop.catalog', ['category' => $cat->slug]) }}"
                           class="inline-block border border-black text-black px-8 py-3 text-xs font-bold uppercase tracking-widest hover:bg-black hover:text-white transition">
                            View All {{ $cat->name }} &rarr;
                        </a>
                    </div>
                @else
                    <div class="py-20 text-center">
                        <p class="text-gray-400 text-sm uppercase tracking-widest">No products in this category yet.</p>
                    </div>
                @endif
            </div>
        @endforeach

    </div>
</section>


{{-- ============================================================ --}}
{{-- SPLIT FEATURE BANNER --}}
{{-- ============================================================ --}}
<section class="py-4 bg-gray-50">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Banner 1 --}}
            <div class="relative overflow-hidden group" style="height: 380px;">
                <img src="https://images.unsplash.com/photo-1551232864-3f0890e580d9?auto=format&fit=crop&w=900&q=80"
                     alt="Men's Collection"
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition-all duration-300"></div>
                <div class="absolute bottom-0 left-0 p-8 text-white">
                    <p class="text-[10px] uppercase tracking-[0.35em] text-white/70 mb-2">Men's Collection</p>
                    <h3 class="font-serif text-3xl font-bold mb-4">Refined Classics</h3>
                    <a href="{{ route('shop.catalog') }}"
                       class="inline-block border border-white text-white text-[10px] font-bold uppercase tracking-widest px-6 py-2.5 hover:bg-white hover:text-black transition">
                        Shop Men
                    </a>
                </div>
            </div>

            {{-- Banner 2 --}}
            <div class="relative overflow-hidden group" style="height: 380px;">
                <img src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=900&q=80"
                     alt="Women's Collection"
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition-all duration-300"></div>
                <div class="absolute bottom-0 left-0 p-8 text-white">
                    <p class="text-[10px] uppercase tracking-[0.35em] text-white/70 mb-2">Women's Collection</p>
                    <h3 class="font-serif text-3xl font-bold mb-4">Contemporary Style</h3>
                    <a href="{{ route('shop.catalog') }}"
                       class="inline-block border border-white text-white text-[10px] font-bold uppercase tracking-widest px-6 py-2.5 hover:bg-white hover:text-black transition">
                        Shop Women
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- ============================================================ --}}
{{-- WHY CHOOSE US --}}
{{-- ============================================================ --}}
<section class="py-12 bg-gray-50 border-t border-b border-gray-100">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="space-y-2">
                <div class="w-10 h-10 mx-auto flex items-center justify-center">
                    <svg class="w-7 h-7 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider text-black">Free Shipping</p>
                <p class="text-[11px] text-gray-500">On orders above Rs. 3000</p>
            </div>
            <div class="space-y-2">
                <div class="w-10 h-10 mx-auto flex items-center justify-center">
                    <svg class="w-7 h-7 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider text-black">Premium Quality</p>
                <p class="text-[11px] text-gray-500">100% authentic fabrics</p>
            </div>
            <div class="space-y-2">
                <div class="w-10 h-10 mx-auto flex items-center justify-center">
                    <svg class="w-7 h-7 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider text-black">Easy Returns</p>
                <p class="text-[11px] text-gray-500">30-day hassle-free returns</p>
            </div>
            <div class="space-y-2">
                <div class="w-10 h-10 mx-auto flex items-center justify-center">
                    <svg class="w-7 h-7 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider text-black">24/7 Support</p>
                <p class="text-[11px] text-gray-500">Always here to help you</p>
            </div>
        </div>
    </div>
</section>

@endsection
