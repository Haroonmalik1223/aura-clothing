@extends('layouts.app', ['title' => 'Shopping Bag | AURA Clothing'])

@section('content')
<div class="bg-white min-h-screen">

    {{-- Header --}}
    <div class="bg-gray-50 border-b border-gray-200 py-8">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-[11px] text-gray-400 uppercase tracking-wider mb-2">
                <a href="{{ route('shop.home') }}" class="hover:text-black transition">Home</a>
                <span>/</span>
                <span class="text-black font-semibold">Shopping Bag</span>
            </nav>
            <h1 class="font-serif text-3xl font-bold text-black">Your Shopping Bag</h1>
        </div>
    </div>

    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-10">

        @if(!empty($cart) && count($cart) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- ========================= --}}
                {{-- Cart Items (8 cols) --}}
                {{-- ========================= --}}
                <div class="lg:col-span-8 space-y-0">

                    {{-- Table Header --}}
                    <div class="hidden sm:grid grid-cols-12 gap-4 pb-3 border-b border-gray-200 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                        <div class="col-span-6">Product</div>
                        <div class="col-span-2 text-center">Price</div>
                        <div class="col-span-2 text-center">Qty</div>
                        <div class="col-span-2 text-right">Subtotal</div>
                    </div>

                    {{-- Items --}}
                    @foreach($cart as $item)
                        <div class="grid grid-cols-12 gap-4 py-6 border-b border-gray-100 items-center">

                            {{-- Image + Info --}}
                            <div class="col-span-12 sm:col-span-6 flex items-center gap-4">
                                <div class="w-20 h-24 flex-shrink-0 overflow-hidden bg-gray-100 border border-gray-200">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['product_name'] }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-grow min-w-0 space-y-1">
                                    <span class="text-[9px] font-bold uppercase tracking-[0.3em] text-gray-400 block">{{ $item['brand'] }}</span>
                                    <a href="{{ route('shop.product', $item['product_slug']) }}"
                                       class="text-sm font-semibold text-black hover:underline leading-snug block">
                                        {{ $item['product_name'] }}
                                    </a>
                                    <p class="text-[11px] text-gray-500 uppercase tracking-wider">{{ $item['variant_title'] }}</p>
                                    <p class="text-[10px] text-gray-400">SKU: {{ $item['sku'] }}</p>

                                    {{-- Mobile price --}}
                                    <div class="flex items-center justify-between mt-2 sm:hidden">
                                        <span class="text-sm font-bold text-black">Rs. {{ number_format($item['price'] * $item['quantity'], 0) }}</span>
                                        <form action="{{ route('shop.cart.remove') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $item['id'] }}">
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 underline transition">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Price --}}
                            <div class="hidden sm:flex col-span-2 items-center justify-center">
                                <span class="text-sm text-gray-700">Rs. {{ number_format($item['price'], 0) }}</span>
                            </div>

                            {{-- Quantity --}}
                            <div class="hidden sm:flex col-span-2 items-center justify-center">
                                <span class="text-sm font-semibold text-black">× {{ $item['quantity'] }}</span>
                            </div>

                            {{-- Subtotal + Remove --}}
                            <div class="hidden sm:flex col-span-2 items-center justify-end gap-3">
                                <span class="text-sm font-bold text-black">Rs. {{ number_format($item['price'] * $item['quantity'], 0) }}</span>
                                <form action="{{ route('shop.cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $item['id'] }}">
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach

                    {{-- Cart actions --}}
                    <div class="pt-4 flex items-center justify-between">
                        <form action="{{ route('shop.cart.clear') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs text-gray-500 hover:text-red-600 uppercase tracking-wider font-medium transition underline">
                                Clear Bag
                            </button>
                        </form>
                        <a href="{{ route('shop.catalog') }}" class="text-xs text-black hover:underline uppercase tracking-wider font-semibold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            Continue Shopping
                        </a>
                    </div>
                </div>

                {{-- ========================= --}}
                {{-- Order Summary (4 cols) --}}
                {{-- ========================= --}}
                <div class="lg:col-span-4">
                    <div class="bg-gray-50 border border-gray-200 p-6 space-y-5 sticky top-24">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-black border-b border-gray-200 pb-4">Order Summary</h2>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-semibold text-black">Rs. {{ number_format($total, 0) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span class="font-semibold {{ $total >= 3000 ? 'text-green-600' : 'text-black' }}">
                                    {{ $total >= 3000 ? 'FREE' : 'Rs. 200' }}
                                </span>
                            </div>
                            @if($total < 3000)
                                <div class="text-[11px] text-gray-500 bg-yellow-50 border border-yellow-200 px-3 py-2 rounded">
                                    Add Rs. {{ number_format(3000 - $total, 0) }} more for free shipping!
                                </div>
                            @endif
                        </div>

                        <div class="border-t border-gray-200 pt-4 flex justify-between items-center">
                            <span class="text-sm font-bold uppercase tracking-wider text-black">Total</span>
                            <span class="text-xl font-bold text-black">
                                Rs. {{ number_format($total + ($total >= 3000 ? 0 : 200), 0) }}
                            </span>
                        </div>

                        <button type="button"
                                onclick="alert('Demo: Dynamic variants order ready to process!')"
                                class="w-full bg-black text-white py-4 text-xs font-bold uppercase tracking-widest hover:bg-gray-900 transition">
                            Proceed to Checkout
                        </button>

                        {{-- Promo Code --}}
                        <div class="pt-2">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Promo Code</p>
                            <div class="flex">
                                <input type="text" placeholder="Enter code..." class="flex-grow border border-gray-300 px-3 py-2 text-xs focus:outline-none focus:border-black">
                                <button class="bg-black text-white px-4 text-xs font-semibold uppercase tracking-wider hover:bg-gray-800 transition">Apply</button>
                            </div>
                        </div>

                        {{-- Trust badges --}}
                        <div class="border-t border-gray-200 pt-4 space-y-2 text-[11px] text-gray-500">
                            <p class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Secure 256-bit SSL Checkout
                            </p>
                            <p class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                30-Day Easy Returns
                            </p>
                            <p class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                100% Authentic Products
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        @else
            {{-- Empty Cart --}}
            <div class="py-24 text-center max-w-md mx-auto space-y-5">
                <svg class="w-16 h-16 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <div class="space-y-2">
                    <h2 class="font-serif text-2xl font-bold text-black">Your bag is empty</h2>
                    <p class="text-sm text-gray-500">Looks like you haven't added anything to your bag yet. Start exploring our latest collections.</p>
                </div>
                <a href="{{ route('shop.catalog') }}"
                   class="inline-block bg-black text-white px-10 py-3.5 text-xs font-bold uppercase tracking-widest hover:bg-gray-900 transition">
                    Shop Now
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
