<div class="group flex flex-col">
    {{-- Image --}}
    <div class="relative overflow-hidden bg-gray-100" style="aspect-ratio: 3/4;">
        <a href="{{ route('shop.product', $product->slug) }}" class="block w-full h-full">
            <img src="{{ $product->image }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                 loading="lazy">
        </a>

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
            <span class="bg-black text-white text-[9px] font-bold uppercase tracking-wider px-2.5 py-1">NEW</span>
        </div>

        {{-- Category tag top-right --}}
        @if($product->category)
            <div class="absolute top-3 right-3">
                <span class="bg-white/90 text-black text-[9px] font-bold uppercase tracking-wider px-2 py-1">
                    {{ $product->category->name }}
                </span>
            </div>
        @endif

        {{-- Slide-up CTA on hover --}}
        <div class="absolute bottom-0 left-0 right-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
            <a href="{{ route('shop.product', $product->slug) }}"
               class="block bg-black text-white text-center text-[11px] font-bold uppercase tracking-widest py-3.5 hover:bg-gray-900 transition">
                Quick View
            </a>
        </div>
    </div>

    {{-- Product Info --}}
    <div class="pt-3 flex flex-col gap-0.5">
        <span class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">{{ $product->brand ?: 'AURA' }}</span>
        <a href="{{ route('shop.product', $product->slug) }}"
           class="text-sm font-semibold text-gray-900 hover:text-black line-clamp-1 leading-snug">
            {{ $product->name }}
        </a>
        {{-- Variant summary --}}
        <div class="flex flex-wrap gap-1 mt-1">
            @foreach($product->available_attributes as $attr)
                <span class="text-[9px] text-gray-500 bg-gray-100 px-1.5 py-0.5 uppercase tracking-wider">
                    {{ $attr['name'] }}: {{ count($attr['values']) }}
                </span>
            @endforeach
        </div>
        <span class="mt-2 text-sm font-bold text-black">{{ $product->price_display }}</span>
    </div>
</div>
