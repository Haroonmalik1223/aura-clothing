@extends('layouts.app', ['title' => 'Collections | AURA Clothing'])

@section('content')
<div class="bg-white min-h-screen">

    {{-- Page Header --}}
    <div class="bg-gray-50 border-b border-gray-200 py-12">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-[11px] text-gray-400 uppercase tracking-wider mb-3">
                <a href="{{ route('shop.home') }}" class="hover:text-black transition">Home</a>
                <span>/</span>
                <span class="text-black font-semibold">Collections</span>
            </nav>
            <h1 class="font-serif text-4xl sm:text-5xl font-bold text-black">Clothing Collections</h1>
            <p class="text-sm text-gray-500 mt-2 max-w-xl">
                Browse our curated categories designed with architectural silhouettes and premium organic fabrics.
            </p>
        </div>
    </div>

    {{-- Collections / Categories Grid --}}
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('shop.catalog', ['category' => $category->slug]) }}"
                   class="group relative overflow-hidden bg-gray-100 block aspect-[4/5] shadow-sm hover:shadow-xl transition-all duration-500">

                    {{-- Image --}}
                    @if($category->image)
                        <img src="{{ $category->image }}" alt="{{ $category->name }}"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300"></div>
                    @endif

                    {{-- Gradient Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent group-hover:from-black/90 transition-all duration-300"></div>

                    {{-- Text Content --}}
                    <div class="absolute bottom-0 left-0 right-0 p-6 text-white space-y-2">
                        <span class="inline-block text-[10px] font-mono uppercase tracking-[0.25em] text-yellow-300 font-semibold">
                            {{ $category->products_count }} {{ Str::plural('Product', $category->products_count) }}
                        </span>
                        <h2 class="font-serif text-2xl font-bold text-white group-hover:text-yellow-200 transition-colors leading-tight">
                            {{ $category->name }}
                        </h2>
                        @if($category->description)
                            <p class="text-xs text-white/70 line-clamp-2 leading-relaxed">
                                {{ $category->description }}
                            </p>
                        @endif
                        <div class="pt-2 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-widest text-white/90 group-hover:text-white">
                            <span>View Collection</span>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
