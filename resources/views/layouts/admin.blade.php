<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Studio | AURA Apparel' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Space Grotesk"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen antialiased flex" x-data="{ mobileSidebarOpen: false }">

    <!-- Mobile Backdrop -->
    <div x-show="mobileSidebarOpen" 
         x-cloak 
         @click="mobileSidebarOpen = false" 
         class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden transition-opacity">
    </div>

    <!-- SIDEBAR -->
    <aside :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
           class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 flex flex-col justify-between transition-transform duration-200 ease-in-out shadow-lg lg:shadow-none">
        
        <!-- Top Portion: Brand & Nav Tabs -->
        <div class="flex flex-col flex-grow overflow-y-auto">
            
            <!-- Brand Logo Header -->
            <div class="h-16 px-6 border-b border-slate-200 flex items-center justify-between">
                <a href="{{ route('admin.products.index') }}" class="flex items-center space-x-3 group">
                    <div class="w-8 h-8 rounded-lg bg-slate-900 text-white font-display font-black text-sm flex items-center justify-center shadow-sm group-hover:bg-slate-800 transition">
                        A
                    </div>
                    <div>
                        <span class="font-display font-extrabold text-base tracking-widest text-slate-900 block leading-tight">AURA</span>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-indigo-600 block">Admin Studio</span>
                    </div>
                </a>

                <!-- Close button for mobile -->
                <button @click="mobileSidebarOpen = false" class="lg:hidden p-1.5 text-slate-400 hover:text-slate-700 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Navigation Sections -->
            <div class="px-4 py-6 space-y-7">
                
                <!-- Section 1: Catalog Management -->
                <div class="space-y-1.5">
                    <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">
                        Apparel & Inventory
                    </div>

                    <!-- Tab: Products & Variants -->
                    <a href="{{ route('admin.products.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.products.index') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.products.index') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span>Products & Variants</span>
                        </div>
                        @php $prodCount = \App\Models\Product::count(); @endphp
                        <span class="text-[11px] font-mono px-2 py-0.5 rounded-full {{ request()->routeIs('admin.products.index') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                            {{ $prodCount }}
                        </span>
                    </a>

                    <!-- Tab: Add Product Flow (Dynamic Variants) -->
                    <a href="{{ route('admin.products.create') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.products.create') ? 'bg-indigo-600 text-white shadow-md' : 'text-indigo-700 bg-indigo-50/70 hover:bg-indigo-100 hover:text-indigo-900' }}">
                        <div class="flex items-center space-x-3">
                            <span class="text-sm">✨</span>
                            <span>Add Product Flow</span>
                        </div>
                        <span class="text-[10px] font-mono uppercase tracking-wider font-bold px-1.5 py-0.5 rounded {{ request()->routeIs('admin.products.create') ? 'bg-white/20 text-white' : 'bg-indigo-200/60 text-indigo-800' }}">
                            Dynamic
                        </span>
                    </a>

                    <!-- Tab: Categories -->
                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.categories.*') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <span>Clothing Categories</span>
                        </div>
                        @php $catCount = \App\Models\Category::count(); @endphp
                        <span class="text-[11px] font-mono px-2 py-0.5 rounded-full {{ request()->routeIs('admin.categories.*') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                            {{ $catCount }}
                        </span>
                    </a>
                </div>

                <!-- Section 2: Storefront & Previews -->
                <div class="space-y-1.5">
                    <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">
                        Live Storefront
                    </div>

                    <a href="{{ route('shop.home') }}" target="_blank" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                        <div class="flex items-center space-x-3">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            <span>Storefront Home</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono">&rarr;</span>
                    </a>

                    <a href="{{ route('shop.catalog') }}" target="_blank" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                        <div class="flex items-center space-x-3">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>Apparel Catalog</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono">&rarr;</span>
                    </a>

                    <a href="{{ route('shop.cart') }}" target="_blank" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                        <div class="flex items-center space-x-3">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span>Shopping Bag</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono">&rarr;</span>
                    </a>
                </div>

            </div>
        </div>

        <!-- Bottom User Card with Logout -->
        <div class="p-4 border-t border-slate-200 bg-slate-50/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-sm">
                        AD
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900 truncate">
                            {{ Auth::check() ? Auth::user()->name : 'AURA Admin' }}
                        </p>
                        <p class="text-[11px] text-slate-500 font-mono truncate">
                            {{ Auth::check() ? Auth::user()->email : 'admin@admiin.com' }}
                        </p>
                    </div>
                </div>

                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Sign Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- Top Mobile Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between lg:hidden sticky top-0 z-30">
            <div class="flex items-center space-x-3">
                <button @click="mobileSidebarOpen = true" class="p-2 text-slate-600 hover:bg-slate-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <span class="font-display font-bold text-slate-900 text-sm">AURA Admin Studio</span>
            </div>

            <a href="{{ route('shop.home') }}" target="_blank" class="text-xs text-indigo-600 font-semibold">Storefront &rarr;</a>
        </header>

        <!-- Flash message alerts -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 w-full">
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-medium flex items-center space-x-2 shadow-xs">
                    <svg class="w-4 h-4 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs font-medium flex items-center space-x-2 shadow-xs">
                    <svg class="w-4 h-4 flex-shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs shadow-xs">
                    <p class="font-bold mb-1">Please address the following:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Main Page Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full flex-grow">
            @yield('content')
        </main>

        <footer class="bg-white border-t border-slate-200 py-4 px-6 text-center text-xs text-slate-500 mt-auto">
            AURA Clothing Brand — Dynamic Variant Management Studio
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
