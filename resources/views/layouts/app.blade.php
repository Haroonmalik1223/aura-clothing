<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AURA | Premium Clothing Brand' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['"Cormorant Garamond"', 'serif'],
                    },
                    colors: {
                        brand: '#1a1a1a',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-white text-gray-900 font-sans antialiased">

    {{-- Announcement Bar --}}
    <div class="bg-black text-white text-[11px] py-2 text-center font-medium tracking-widest uppercase" x-data="{ show: true }" x-show="show">
        <div class="flex items-center justify-center gap-6">
            <span>Free Shipping on Orders Over Rs. 3000</span>
            <span class="text-gray-500">|</span>
            <span>Use Code <strong class="text-yellow-400">AURA20</strong> for 20% Off</span>
            <span class="text-gray-500">|</span>
            <a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition">Admin →</a>
            <button @click="show = false" class="ml-4 text-gray-500 hover:text-white absolute right-4">✕</button>
        </div>
    </div>

    {{-- Main Navigation --}}
    <header class="sticky top-0 z-50 bg-white border-b border-gray-200" x-data="{ mobileOpen: false }">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('shop.home') }}" class="flex flex-col leading-none">
                    <span class="font-serif text-2xl font-bold tracking-[0.3em] text-black uppercase">AURA</span>
                    <span class="text-[8px] tracking-[0.4em] text-gray-400 uppercase font-medium">Clothing</span>
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden md:flex items-center gap-8 text-[11px] font-semibold uppercase tracking-widest">
                    <a href="{{ route('shop.home') }}" class="text-gray-900 hover:text-black transition border-b-2 {{ request()->routeIs('shop.home') ? 'border-black' : 'border-transparent' }} pb-0.5">Home</a>
                    <a href="{{ route('shop.catalog') }}" class="text-gray-600 hover:text-black transition border-b-2 {{ request()->routeIs('shop.catalog') && !request('category') ? 'border-black text-black' : 'border-transparent' }} pb-0.5">New Arrivals</a>

                    {{-- Categories Dropdown --}}
                    <div class="relative py-4 flex items-center" x-data="{ open: false, timer: null }"
                         @mouseenter="clearTimeout(timer); open = true"
                         @mouseleave="timer = setTimeout(() => open = false, 200)">
                        <a href="{{ route('shop.collections') }}"
                           class="text-gray-600 hover:text-black transition flex items-center gap-1 pb-0.5 border-b-2 {{ request()->routeIs('shop.collections') ? 'border-black text-black' : 'border-transparent' }}">
                            Collections
                            <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                        <div x-show="open" x-cloak
                             @mouseenter="clearTimeout(timer); open = true"
                             @mouseleave="timer = setTimeout(() => open = false, 200)"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute top-full left-1/2 -translate-x-1/2 w-64 bg-white border border-gray-200 shadow-xl rounded-none z-50 before:content-[''] before:absolute before:-top-4 before:left-0 before:right-0 before:h-4">
                            @php
                                $navCats = \App\Models\Category::active()->withCount('products')->get();
                            @endphp
                            @foreach($navCats as $cat)
                                <a href="{{ route('shop.catalog', ['category' => $cat->slug]) }}"
                                   class="flex items-center justify-between px-5 py-3 text-[11px] uppercase tracking-wider font-medium text-gray-700 hover:bg-gray-50 hover:text-black transition border-b border-gray-100 last:border-0">
                                    <span>{{ $cat->name }}</span>
                                    <span class="text-gray-400 text-[10px]">({{ $cat->products_count }})</span>
                                </a>
                            @endforeach
                            <a href="{{ route('shop.collections') }}"
                               class="flex items-center justify-between px-5 py-2.5 bg-gray-50 text-[10px] uppercase tracking-widest font-bold text-black hover:bg-gray-100 transition border-t border-gray-200">
                                <span>All Collections &rarr;</span>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('shop.catalog') }}" class="text-gray-600 hover:text-black transition border-b-2 border-transparent pb-0.5">Sale</a>
                </nav>

                {{-- Right Icons --}}
                <div class="flex items-center gap-3">
                    {{-- Search --}}
                    <div class="hidden sm:block" x-data="{ open: false }">
                        <button @click="open = !open" class="p-2 text-gray-600 hover:text-black transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false"
                             class="absolute top-16 right-0 left-0 bg-white border-b border-gray-200 z-50 px-6 py-4 shadow-sm">
                            <form action="{{ route('shop.catalog') }}" method="GET" class="max-w-2xl mx-auto flex gap-2">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search for products..." autofocus
                                       class="flex-grow border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-black">
                                <button type="submit" class="bg-black text-white px-6 py-2.5 text-xs font-semibold uppercase tracking-wider hover:bg-gray-800 transition">Search</button>
                            </form>
                        </div>
                    </div>

                    {{-- Cart --}}
                    @php
                        $cartSession = session()->get('cart', []);
                        $cartCount = array_sum(array_column($cartSession, 'quantity'));
                    @endphp
                    <a href="{{ route('shop.cart') }}" class="relative p-2 text-gray-700 hover:text-black transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @if($cartCount > 0)
                            <span class="absolute top-0.5 right-0.5 bg-black text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>

                    {{-- Mobile Hamburger --}}
                    <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-gray-700">
                        <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileOpen" x-cloak class="md:hidden bg-white border-t border-gray-100">
            <div class="px-4 py-4 space-y-1">
                <a href="{{ route('shop.home') }}" class="block py-2.5 text-xs font-semibold uppercase tracking-widest text-gray-800 border-b border-gray-100">Home</a>
                <a href="{{ route('shop.catalog') }}" class="block py-2.5 text-xs font-semibold uppercase tracking-widest text-gray-600 border-b border-gray-100">New Arrivals</a>
                @foreach($navCats ?? [] as $cat)
                    <a href="{{ route('shop.catalog', ['category' => $cat->slug]) }}" class="block py-2.5 text-xs font-semibold uppercase tracking-widest text-gray-600 border-b border-gray-100 pl-3">{{ $cat->name }}</a>
                @endforeach
                <a href="{{ route('login') }}" class="block py-2.5 text-xs font-semibold uppercase tracking-widest text-gray-400">Admin Panel</a>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border-b border-green-200 text-green-800 px-4 py-3 text-center text-xs font-medium flex items-center justify-center gap-2">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-b border-red-200 text-red-800 px-4 py-3 text-center text-xs font-medium flex items-center justify-center gap-2">
            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-black text-white mt-20">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-gray-800">
                {{-- Brand --}}
                <div class="space-y-5">
                    <div>
                        <span class="font-serif text-3xl font-bold tracking-[0.3em] uppercase">AURA</span>
                        <span class="block text-[9px] tracking-[0.4em] text-gray-500 uppercase mt-0.5">Premium Clothing</span>
                    </div>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Contemporary Pakistani clothing brand offering premium quality fabrics with modern cuts and timeless style.
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="#" class="w-8 h-8 border border-gray-700 flex items-center justify-center text-gray-400 hover:text-white hover:border-white transition text-xs">f</a>
                        <a href="#" class="w-8 h-8 border border-gray-700 flex items-center justify-center text-gray-400 hover:text-white hover:border-white transition text-xs">in</a>
                        <a href="#" class="w-8 h-8 border border-gray-700 flex items-center justify-center text-gray-400 hover:text-white hover:border-white transition text-xs">ig</a>
                    </div>
                </div>

                {{-- Collections --}}
                <div class="space-y-4">
                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Collections</h4>
                    <ul class="space-y-2.5 text-xs text-gray-500">
                        @foreach(\App\Models\Category::active()->take(5)->get() as $footCat)
                            <li><a href="{{ route('shop.catalog', ['category' => $footCat->slug]) }}" class="hover:text-white transition">{{ $footCat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Help --}}
                <div class="space-y-4">
                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Customer Care</h4>
                    <ul class="space-y-2.5 text-xs text-gray-500">
                        <li><a href="#" class="hover:text-white transition">Size Guide</a></li>
                        <li><a href="#" class="hover:text-white transition">Shipping & Returns</a></li>
                        <li><a href="#" class="hover:text-white transition">Track Your Order</a></li>
                        <li><a href="#" class="hover:text-white transition">FAQs</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition text-gray-600">Admin Panel</a></li>
                    </ul>
                </div>

                {{-- Newsletter --}}
                <div class="space-y-4">
                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Stay in the Loop</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">Subscribe for exclusive deals, new arrivals & style inspiration.</p>
                    <div class="flex">
                        <input type="email" placeholder="Your email address" class="flex-grow bg-gray-900 border border-gray-700 text-xs px-3 py-2.5 text-white placeholder-gray-600 focus:outline-none focus:border-white">
                        <button class="bg-white hover:bg-gray-200 text-black px-4 text-xs font-bold uppercase tracking-wider transition">Go</button>
                    </div>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-gray-600">
                <p>&copy; {{ date('Y') }} AURA Clothing. All Rights Reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition">Terms & Conditions</a>
                    <a href="#" class="hover:text-white transition">Refund Policy</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Floating AI Personal Stylist & Storefront Chatbot --}}
    @include('partials.chatbot')

    @stack('scripts')
</body>
</html>
