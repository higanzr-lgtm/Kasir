<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Warung Kopi Kita - Pesan Online</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        /* ====== GOOGLE FONTS (Inter + Plus Jakarta Sans) ====== */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * { font-family: 'Inter', 'Plus Jakarta Sans', system-ui, sans-serif; }
        
        html { scroll-behavior: smooth; }

        /* ====== CUSTOM SCROLLBAR ====== */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #10b981; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #059669; }

        /* ====== ANIMATIONS ====== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideInRight { from { opacity: 0; transform: translateX(100px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes slideInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        @keyframes scaleIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
        .animate-fade-in { animation: fadeIn 0.4s ease-out; }
        .animate-slide-in-right { animation: slideInRight 0.4s ease-out; }
        .animate-slide-in-up { animation: slideInUp 0.5s ease-out; }
        .animate-bounce { animation: bounce 1s infinite; }
        .animate-pulse { animation: pulse 2s infinite; }
        .animate-scale-in { animation: scaleIn 0.3s ease-out; }

        .product-card {
            animation: fadeInUp 0.5s ease-out;
            animation-fill-mode: both;
        }
        .product-card:nth-child(1) { animation-delay: 0.05s; }
        .product-card:nth-child(2) { animation-delay: 0.1s; }
        .product-card:nth-child(3) { animation-delay: 0.15s; }
        .product-card:nth-child(4) { animation-delay: 0.2s; }
        .product-card:nth-child(5) { animation-delay: 0.25s; }
        .product-card:nth-child(6) { animation-delay: 0.3s; }
        .product-card:nth-child(7) { animation-delay: 0.35s; }
        .product-card:nth-child(8) { animation-delay: 0.4s; }
        .product-card:nth-child(9) { animation-delay: 0.45s; }
        .product-card:nth-child(10) { animation-delay: 0.5s; }

        /* ====== CART FLOATING BUTTON (Mobile) ====== */
        .cart-float-btn {
            display: none;
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 40;
            box-shadow: 0 8px 32px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
        }
        .cart-float-btn:hover { transform: scale(1.08); box-shadow: 0 12px 40px rgba(16, 185, 129, 0.5); }
        .cart-float-btn:active { transform: scale(0.95); }

        /* ====== BADGE CART COUNT ====== */
        .cart-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ef4444;
            color: white;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
            animation: scaleIn 0.3s ease-out;
        }

        /* ====== MODAL OVERLAY ====== */
        .modal-overlay {
            animation: fadeIn 0.2s ease-out;
        }

        /* ====== CART DRAWER (Mobile) ====== */
        .cart-drawer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 50;
            transform: translateY(100%);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 20px 20px 0 0;
            max-height: 85vh;
        }
        .cart-drawer.open { transform: translateY(0); }

        .drawer-handle {
            width: 36px;
            height: 4px;
            background: #d1d5db;
            border-radius: 4px;
            margin: 0 auto 12px;
        }

        /* ====== MAP ====== */
        #map {
            height: 200px;
            width: 100%;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            z-index: 1;
        }

        /* ====== SHIMMER LOADING ====== */
        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        /* ====== SEARCH RESULTS DROPDOWN ====== */
        #search-results {
            z-index: 1000;
            border-radius: 12px;
            overflow: hidden;
        }
        #search-results div:last-child { border-bottom: none !important; }

        /* ====== STICKY HEADER ====== */
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 30;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* ====== MOBILE BOTTOM NAV ====== */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 35;
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 6px 0;
            padding-bottom: max(6px, env(safe-area-inset-bottom));
            box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
        }
        .bottom-nav a {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            font-size: 10px;
            color: #9ca3af;
            transition: all 0.2s;
            padding: 4px 0;
            text-decoration: none;
        }
        .bottom-nav a.active { color: #059669; }
        .bottom-nav a i { font-size: 20px; transition: transform 0.2s; }
        .bottom-nav a:active i { transform: scale(0.85); }

        /* ====== RESPONSIVE ====== */
        @media (max-width: 1023px) {
            .cart-float-btn { display: flex !important; }
            .bottom-nav { display: flex !important; }
            .desktop-cart { display: none !important; }
            body { padding-bottom: 64px; }
            main { padding-bottom: 0 !important; }
        }

        @media (min-width: 1024px) {
            .bottom-nav { display: none !important; }
            .cart-float-btn { display: none !important; }
            .desktop-cart { display: block !important; }
            body { padding-bottom: 0; }
        }

        /* ====== PRODUCT CARD HOVER EFFECTS ====== */
        .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        }
        .product-card:active {
            transform: scale(0.98);
        }
        .product-card .product-img {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .product-card:hover .product-img {
            transform: scale(1.08);
        }

        /* ====== MODAL RESPONSIVE ====== */
        @media (max-width: 480px) {
            .modal-content {
                margin: 0 8px;
                padding: 20px;
                border-radius: 16px;
            }
            #qris-image {
                width: 180px !important;
                height: 180px !important;
            }
        }

        /* ====== INPUT STYLING ====== */
        .input-custom {
            border: 2px solid #e5e7eb;
            transition: all 0.2s;
        }
        .input-custom:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
            outline: none;
        }
        .input-custom:hover {
            border-color: #d1d5db;
        }

        /* ====== toast ====== */
        .toast {
            position: fixed;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: #1f2937;
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            z-index: 100;
            opacity: 0;
            transition: all 0.3s ease;
            pointer-events: none;
            white-space: nowrap;
        }
        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* ====== STATUS BADGE ====== */
        .status-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-50 via-white to-teal-50 min-h-screen">

    <!-- ====== TOAST NOTIFICATION ====== -->
    <div id="toast" class="toast">
        <i class="fa-solid fa-check-circle text-emerald-400 mr-2"></i>
        <span id="toast-message">Berhasil!</span>
    </div>

    <!-- ====== NAVBAR ====== -->
    <header class="sticky-header bg-white/90 shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-mug-hot text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-extrabold text-gray-900 leading-tight">Warung Kopi Kita</h1>
                        <p class="text-[10px] text-emerald-600 font-semibold tracking-wide">PESAN ONLINE</p>
                    </div>
                </div>

                <!-- Desktop Right Menu -->
                <div class="flex items-center gap-2">
                    <button onclick="showRiwayatModal()" class="hidden sm:flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-3.5 py-2 rounded-xl transition-all cursor-pointer">
                        <i class="fa-solid fa-clock-rotate-left text-emerald-500"></i>
                        <span class="hidden md:inline">Riwayat</span>
                    </button>
                    <div class="flex items-center gap-2 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100">
                        <div class="w-7 h-7 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-user text-white text-xs"></i>
                        </div>
                        <span class="text-sm font-semibold text-emerald-800 max-w-[100px] truncate">{{ session('nama', 'Customer') }}</span>
                    </div>
                    <a href="{{ route('logout') }}" class="bg-red-50 hover:bg-red-100 text-red-500 p-2 rounded-xl transition-all" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- ====== HERO SECTION (Mobile Greeting) ====== -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white lg:hidden">
        <div class="px-4 py-4">
            <p class="text-emerald-100 text-xs font-medium">Selamat datang kembali 👋</p>
            <h2 class="text-xl font-bold mt-0.5">{{ session('nama', 'Customer') }}</h2>
            <p class="text-emerald-200 text-xs mt-0.5">Pesan kopi favoritmu sekarang!</p>
        </div>
    </div>

    <!-- ====== SEARCH BAR (Mobile) ====== -->
    <div class="px-4 pt-3 pb-2 lg:hidden">
        <div class="relative">
            <input type="text" id="search-produk-mobile" placeholder="Cari menu favoritmu..."
                class="w-full border-2 border-gray-200 rounded-2xl py-3 pl-11 pr-4 text-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200 transition-all bg-white shadow-sm">
            <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    <!-- ====== CATEGORY PILLS (Mobile) ====== -->
    <div class="px-4 pb-3 overflow-x-auto lg:hidden">
        <div class="flex gap-2 whitespace-nowrap" id="category-pills">
            <button class="category-pill active px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-full transition-all" data-category="all">
                <i class="fa-solid fa-utensils mr-1"></i> Semua
            </button>
            <button class="category-pill px-4 py-2 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full hover:bg-gray-200 transition-all" data-category="minuman">
                <i class="fa-solid fa-mug-saucer mr-1"></i> Minuman
            </button>
            <button class="category-pill px-4 py-2 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full hover:bg-gray-200 transition-all" data-category="makanan">
                <i class="fa-solid fa-bowl-food mr-1"></i> Makanan
            </button>
            <button class="category-pill px-4 py-2 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full hover:bg-gray-200 transition-all" data-category="promo">
                <i class="fa-solid fa-tags mr-1"></i> Promo
            </button>
        </div>
    </div>

    <!-- ====== MAIN CONTENT ====== -->
    <main class="max-w-7xl mx-auto px-4 pb-6 lg:py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- ====== DAFTAR PRODUK ====== -->
            <div class="lg:col-span-2">
                <!-- Header Desktop -->
                <div class="hidden lg:flex justify-between items-center mb-5">
                    <div>
                        <h2 class="text-xl font-extrabold text-gray-900">
                            <i class="fa-solid fa-mug-hot text-emerald-500 mr-2"></i> Menu Kami
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Pilih menu favorit dan pesan sekarang!</p>
                    </div>
                    <div class="relative w-72">
                        <input type="text" id="search-produk" placeholder="Cari menu..."
                            class="w-full border-2 border-gray-200 rounded-2xl py-2.5 pl-10 pr-4 text-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200 transition-all bg-white shadow-sm">
                        <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Bundle/Paket Menu Section -->
                @if(count($bundles) > 0)
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                            <div class="w-7 h-7 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-gift text-white text-xs"></i>
                            </div>
                            Menu Bundle Hemat
                        </h2>
                        <span class="text-xs text-gray-400">{{ count($bundles) }} paket</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-3 lg:gap-4">
                        @foreach($bundles as $bundle)
                        <div class="product-card bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl overflow-hidden border border-orange-200 shadow-sm hover:shadow-lg transition-all">
                            <div class="relative overflow-hidden aspect-[4/3] bg-orange-100">
                                @if($bundle->foto && file_exists(public_path('images/menu/' . $bundle->foto)))
                                <img src="{{ asset('images/menu/' . $bundle->foto) }}" alt="{{ $bundle->nama_bundle }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-solid fa-gift text-5xl text-orange-300"></i>
                                </div>
                                @endif
                                <div class="absolute top-2 left-2 bg-gradient-to-r from-orange-500 to-red-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-md">
                                    <i class="fa-solid fa-fire mr-0.5"></i> HEMAT
                                </div>
                                @if($bundle->hemat > 0)
                                <div class="absolute top-2 right-2 bg-red-500 text-white text-[8px] font-bold px-2 py-0.5 rounded-full shadow-md">
                                    -Rp{{ number_format($bundle->hemat, 0, ',', '.') }}
                                </div>
                                @endif
                            </div>
                            <div class="p-3">
                                <h3 class="font-bold text-gray-800 text-sm">{{ $bundle->nama_bundle }}</h3>
                                @if($bundle->deskripsi)
                                <p class="text-[10px] text-gray-500 mt-0.5 line-clamp-1">{{ $bundle->deskripsi }}</p>
                                @endif
                                <!-- Daftar item dalam bundle -->
                                <div class="mt-1.5 space-y-0.5">
                                    @foreach($bundle->items as $item)
                                    <div class="flex items-center gap-1 text-[9px] text-gray-500">
                                        <i class="fa-solid fa-plus text-[6px] text-orange-400"></i>
                                        <span>{{ $item->produk->nama_produk ?? 'N/A' }} <strong>x{{ $item->qty }}</strong></span>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-2 flex items-center gap-1.5">
                                    <span class="text-[10px] text-gray-400 line-through">Rp {{ number_format($bundle->total_harga_normal, 0, ',', '.') }}</span>
                                    <span class="text-sm font-extrabold text-red-600">Rp {{ number_format($bundle->harga_bundle, 0, ',', '.') }}</span>
                                </div>
                                <button onclick="tambahBundleKeKeranjang({{ $bundle->id }})"
                                    class="mt-2 w-full bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs py-2.5 rounded-xl hover:from-orange-600 hover:to-red-600 transition-all font-bold shadow-md shadow-orange-200/50 active:scale-95 cursor-pointer flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-cart-plus"></i> Ambil Paket
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Grid Produk -->
                <div id="menu-produk" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-3 lg:gap-4">
                    @php $delay = 0.05; @endphp
                    @foreach($produks as $produk)
                    @php
                        $minumanKeywords = ['kopi', 'teh', 'jus', 'mineral', 'susu', 'milkshake', 'matcha', 'espresso', 'cappuccino', 'air', 'es'];
                        $isMinuman = false;
                        $namaLower = strtolower($produk->nama_produk);
                        foreach ($minumanKeywords as $kw) {
                            if (strpos($namaLower, $kw) !== false) { $isMinuman = true; break; }
                        }
                        $kategori = $isMinuman ? 'minuman' : 'makanan';
                    @endphp
                    <div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl produk-card" 
                         data-nama="{{ strtolower($produk->nama_produk) }}"
                         data-kategori="{{ $kategori }}"
                         style="animation-delay: {{ $delay }}s">
                        @php $delay += 0.05; @endphp
                        
                        <!-- Image Container -->
                        <div class="relative overflow-hidden aspect-[4/3] bg-gray-50">
                            <img src="{{ asset('images/menu/' . $produk->foto) }}" 
                                 alt="{{ $produk->nama_produk }}" 
                                 class="product-img w-full h-full object-cover"
                                 loading="lazy"
                                 onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23e5e7eb%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2250%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%239ca3af%22 font-size=%2212%22>No Image</text></svg>'">
                            
                            @if($produk->diskon)
                            <div class="absolute top-2 left-2 bg-gradient-to-r from-red-500 to-orange-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-md flex items-center gap-1">
                                <i class="fa-solid fa-bolt"></i>
                                {{ $produk->diskon->nama_diskon }}
                            </div>
                            @endif
                            
                            @if($produk->diskon && $produk->diskon->tipe_diskon === 'Persen')
                            <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm text-red-500 text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-sm">
                                -{{ number_format($produk->diskon->nilai, 0) }}%
                            </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="p-3 lg:p-4">
                            <h3 class="font-bold text-gray-800 text-sm lg:text-base leading-tight line-clamp-2">{{ $produk->nama_produk }}</h3>
                            
                            @if($produk->diskon)
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="text-[10px] text-gray-400 line-through">Rp {{ number_format($produk->harga_normal, 0, ',', '.') }}</span>
                                    <span class="text-sm lg:text-base font-extrabold text-emerald-600">Rp {{ number_format($produk->getHargaNet(), 0, ',', '.') }}</span>
                                </div>
                            @else
                                <p class="text-sm lg:text-base font-extrabold text-gray-900 mt-1">Rp {{ number_format($produk->harga_normal, 0, ',', '.') }}</p>
                            @endif

                            <!-- Stok Indicator -->
                            @if($produk->stok && $produk->stok <= 5)
                                <p class="text-[9px] text-orange-500 font-semibold mt-1 flex items-center gap-0.5">
                                    <i class="fa-solid fa-circle-exclamation"></i> Sisa {{ $produk->stok }}
                                </p>
                            @elseif($produk->stok && $produk->stok > 5)
                                <p class="text-[9px] text-emerald-500 font-semibold mt-1 flex items-center gap-0.5">
                                    <i class="fa-solid fa-circle-check"></i> Tersedia
                                </p>
                            @endif

                            <button onclick="tambahKeKeranjang('{{ $produk->id_produk }}', '{{ $produk->nama_produk }}', {{ $produk->getHargaNet() }})" 
                                class="mt-2 lg:mt-3 w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs lg:text-sm py-2.5 lg:py-2.5 rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all font-bold shadow-md shadow-emerald-200/50 active:scale-95 cursor-pointer flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-plus"></i>
                                <span>Keranjang</span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- ====== KERANJANG (Desktop) ====== -->
            <div class="desktop-cart">
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 lg:sticky lg:top-20">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-shopping-cart text-white text-sm"></i>
                            </div>
                            Pesanan Saya
                        </h2>
                        <span id="desktop-cart-count" class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2 py-0.5 rounded-full hidden">0</span>
                    </div>

                    <div id="keranjang-list" class="space-y-3 divide-y divide-gray-100 max-h-72 overflow-y-auto pr-1">
                        <div class="flex flex-col items-center justify-center py-10 text-gray-300">
                            <i class="fa-solid fa-basket-shopping text-5xl mb-3"></i>
                            <p class="text-sm text-gray-400 font-medium">Belum ada pesanan</p>
                            <p class="text-xs text-gray-300 mt-1">Klik tombol Keranjang pada menu</p>
                        </div>
                    </div>

                    <div class="border-t pt-4 mt-2">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-500 text-sm font-medium">Total</span>
                            <div class="text-right">
                                <span id="grand-total" class="text-2xl font-extrabold text-emerald-600">Rp 0</span>
                            </div>
                        </div>
                        <button onclick="showCheckoutModal()" id="btn-pesan" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-extrabold py-3 rounded-2xl hover:from-emerald-700 hover:to-teal-700 transition-all disabled:from-gray-200 disabled:to-gray-300 disabled:text-gray-400 disabled:cursor-not-allowed shadow-lg shadow-emerald-200/50 active:scale-[0.98]" disabled>
                            <i class="fa-solid fa-paper-plane mr-2"></i> Pesan Sekarang
                        </button>
                        <p class="text-[10px] text-gray-400 text-center mt-2">
                            <i class="fa-solid fa-shield-halved text-emerald-400 mr-1"></i>Pesanan diproses cepat & aman
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ====== FLOATING CART BUTTON (Mobile) ====== -->
    <button onclick="toggleCartDrawer()" id="float-cart-btn" class="cart-float-btn bg-gradient-to-r from-emerald-500 to-teal-500 text-white w-14 h-14 rounded-2xl items-center justify-center shadow-xl cursor-pointer">
        <i class="fa-solid fa-bag-shopping text-xl"></i>
        <span id="cart-badge" class="cart-badge hidden">0</span>
    </button>

    <!-- ====== CART DRAWER (Mobile) ====== -->
    <div id="cart-overlay" class="hidden fixed inset-0 bg-black/40 z-40 modal-overlay lg:hidden" onclick="toggleCartDrawer()"></div>
    <div id="cart-drawer" class="cart-drawer bg-white shadow-2xl lg:hidden">
        <div class="p-5 pb-2">
            <div class="drawer-handle"></div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-shopping-cart text-emerald-500"></i>
                    Pesanan Saya
                </h2>
                <span id="mobile-cart-count" class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2 py-0.5 rounded-full hidden">0</span>
            </div>
        </div>

        <div id="keranjang-list-mobile" class="px-5 space-y-3 divide-y divide-gray-100 max-h-[40vh] overflow-y-auto flex-1">
            <div class="flex flex-col items-center justify-center py-10 text-gray-300">
                <i class="fa-solid fa-basket-shopping text-4xl mb-3"></i>
                <p class="text-sm text-gray-400 font-medium">Belum ada pesanan</p>
                <p class="text-xs text-gray-300 mt-1">Pilih menu untuk mulai pesan</p>
            </div>
        </div>

        <div class="p-5 border-t border-gray-100 mt-2">
            <div class="flex justify-between items-center mb-3">
                <span class="text-gray-500 text-sm font-medium">Total</span>
                <span id="mobile-grand-total" class="text-xl font-extrabold text-emerald-600">Rp 0</span>
            </div>
            <div class="flex gap-2">
                <button onclick="toggleCartDrawer()" class="flex-1 bg-gray-100 text-gray-600 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all active:scale-95">
                    Lanjut Belanja
                </button>
                <button onclick="showCheckoutModal(); toggleCartDrawer();" id="btn-pesan-mobile" class="flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-extrabold py-3 rounded-2xl hover:from-emerald-700 hover:to-teal-700 transition-all disabled:from-gray-200 disabled:to-gray-300 disabled:text-gray-400 disabled:cursor-not-allowed shadow-lg shadow-emerald-200/50 active:scale-[0.98]" disabled>
                    <i class="fa-solid fa-paper-plane mr-1"></i> Pesan
                </button>
            </div>
        </div>
    </div>

    <!-- ====== BOTTOM NAVIGATION (Mobile) ====== -->
    <nav class="bottom-nav">
        <a href="#" class="active" onclick="event.preventDefault(); window.scrollTo({top: 0, behavior: 'smooth'});">
            <i class="fa-solid fa-home"></i>
            <span>Beranda</span>
        </a>
        <a href="#" onclick="event.preventDefault(); document.getElementById('search-produk-mobile')?.focus();">
            <i class="fa-solid fa-search"></i>
            <span>Cari</span>
        </a>
        <a href="#" onclick="event.preventDefault(); toggleCartDrawer();" class="relative">
            <i class="fa-solid fa-bag-shopping"></i>
            <span>Pesanan</span>
            <span id="nav-cart-badge" class="absolute -top-0.5 right-1/2 translate-x-6 bg-red-500 text-white text-[8px] font-bold w-4 h-4 rounded-full flex items-center justify-center hidden">0</span>
        </a>
        <a href="#" onclick="event.preventDefault(); showRiwayatModal();">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>Riwayat</span>
        </a>
        <a href="{{ route('logout') }}">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Keluar</span>
        </a>
    </nav>

    <!-- ====== MODAL CHECKOUT ====== -->
    <div id="checkout-modal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-3 sm:p-4 modal-overlay">
        <div class="bg-white rounded-3xl p-5 sm:p-6 max-w-md w-full shadow-2xl overflow-y-auto max-h-[90vh] animate-scale-in">
            <!-- Header -->
            <div class="flex items-center gap-3 mb-5">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-receipt text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-gray-900">Konfirmasi Pesanan</h3>
                    <p class="text-xs text-gray-500">Periksa kembali pesanan Anda</p>
                </div>
            </div>
            
            <!-- Info Ringkas -->
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-4 mb-4 space-y-2.5 text-sm border border-emerald-100">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 flex items-center gap-1.5">
                        <i class="fa-solid fa-receipt text-emerald-400 text-xs"></i> Pesanan:
                    </span>
                    <span id="checkout-nama-pesanan" class="font-bold text-gray-800 text-right max-w-[200px] truncate"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 flex items-center gap-1.5">
                        <i class="fa-solid fa-tag text-emerald-400 text-xs"></i> Total:
                    </span>
                    <span id="checkout-total-harga" class="font-extrabold text-emerald-600 text-lg"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 flex items-center gap-1.5">
                        <i class="fa-solid fa-hashtag text-emerald-400 text-xs"></i> No. Pesanan:
                    </span>
                    <span id="checkout-nomor-pesanan" class="font-bold bg-white px-3 py-1 rounded-lg text-gray-800 shadow-sm"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 flex items-center gap-1.5">
                        <i class="fa-solid fa-qrcode text-emerald-400 text-xs"></i> Pembayaran:
                    </span>
                    <span class="font-bold text-gray-800 flex items-center gap-1.5">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/02/QRIS_logo.svg/1200px-QRIS_logo.svg.png" alt="QRIS" class="h-4">
                        QRIS
                    </span>
                </div>
            </div>

            <!-- Form Checkout -->
            <div class="space-y-4">
                <div>
                    <label for="nomor_hp" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        <i class="fa-solid fa-phone text-emerald-500 mr-1"></i> Nomor HP
                    </label>
                    <input type="tel" id="nomor_hp" name="nomor_hp" 
                        class="input-custom block w-full rounded-2xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-3"
                        placeholder="08xxxxxxxxxx" 
                        inputmode="numeric" 
                        pattern="[0-9]*" 
                        onkeypress="return hanyaAngka(event)">
                </div>

                <div>
                    <label for="deskripsi_pesanan" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        <i class="fa-solid fa-note-sticky text-emerald-500 mr-1"></i> Deskripsi Pesanan
                    </label>
                    <textarea id="deskripsi_pesanan" name="deskripsi_pesanan" rows="3" 
                        class="input-custom block w-full rounded-2xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-3 resize-none"
                        placeholder="Contoh: Kopinya jangan pakai gula, roti bakarnya pakai keju..."></textarea>
                </div>

                <!-- Ringkasan Pesanan -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-4">
                    <p class="font-bold text-gray-700 text-sm mb-3 flex items-center gap-1.5">
                        <i class="fa-solid fa-receipt text-emerald-500"></i> Ringkasan Pesanan
                    </p>
                    <div id="checkout-ringkasan-items" class="space-y-2 text-sm text-gray-600 divide-y divide-gray-200">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex gap-3">
                <button onclick="hideCheckoutModal()" class="flex-1 bg-gray-100 text-gray-600 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all active:scale-95">
                    Batal
                </button>
                <button onclick="submitCheckout()" class="flex-[2] bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-extrabold py-3 rounded-2xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-200/50 active:scale-[0.98]">
                    <i class="fa-solid fa-check mr-1"></i> Konfirmasi Pesanan
                </button>
            </div>
        </div>
    </div>

    <!-- ====== MODAL QRIS ====== -->
    <div id="qrisModal" class="hidden fixed inset-0 bg-black/60 z-50 items-center justify-center p-3 sm:p-4 modal-overlay">
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-2xl w-full max-w-sm text-center animate-scale-in">
            <!-- Header -->
            <div class="flex items-center justify-center gap-2 mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center shadow-md">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/02/QRIS_logo.svg/1200px-QRIS_logo.svg.png" alt="QRIS" class="h-5 brightness-0 invert">
                </div>
                <h2 class="text-lg font-extrabold text-gray-900">Pembayaran QRIS</h2>
            </div>
            
            <!-- Info Tagihan -->
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-4 mb-3 text-sm border border-emerald-100">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Total Bayar:</span>
                    <span id="qris-modal-total" class="font-extrabold text-emerald-600 text-lg"></span>
                </div>
                <div class="flex justify-between items-center mt-1.5">
                    <span class="text-gray-500">Merchant:</span>
                    <span class="font-bold text-gray-800">Warung Kopi Kita</span>
                </div>
                <div class="flex justify-between items-center mt-1.5">
                    <span class="text-gray-500">No. Pesanan:</span>
                    <span id="qris-nomor-pesanan" class="font-bold bg-white px-3 py-0.5 rounded-lg text-gray-800 shadow-sm"></span>
                </div>
            </div>

            <!-- QR Code -->
            <div class="bg-white border-2 border-dashed border-emerald-200 rounded-2xl p-3 mb-3 relative mx-auto max-w-[240px]">
                <div id="qris-loader" class="hidden absolute inset-0 flex items-center justify-center bg-white/80 rounded-2xl z-10">
                    <div class="flex flex-col items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin text-3xl text-emerald-500"></i>
                        <span class="text-xs text-gray-500">Memproses...</span>
                    </div>
                </div>
                <img id="qris-image" src="" alt="QR Code" class="w-full h-auto mx-auto">
                <p class="text-[9px] text-gray-400 mt-1.5">ID: <span id="qris-trx-id-display" class="font-mono text-[10px]"></span></p>
            </div>

            <!-- Cara Bayar -->
            <div class="bg-blue-50 rounded-2xl p-3 mb-3 text-xs text-blue-700 text-left">
                <p class="font-bold flex items-center gap-1.5 mb-1.5">
                    <i class="fa-solid fa-circle-info"></i> Cara Bayar:
                </p>
                <ol class="space-y-0.5 text-blue-600">
                    <li>1. Buka aplikasi <strong>Gojek / OVO / Dana / ShopeePay / M-Banking</strong></li>
                    <li>2. Pilih menu <strong>Bayar → QRIS</strong></li>
                    <li>3. Scan QR Code di samping</li>
                    <li>4. Bayar senilai <strong>Rp <span id="qris-modal-total2"></span></strong></li>
                    <li>5. Klik tombol "Cek Pembayaran" setelah bayar</li>
                </ol>
            </div>

            <!-- Status -->
            <div id="qris-status-area" class="text-xs text-center mb-3">
                <span id="qris-status-text" class="text-gray-400 flex items-center justify-center gap-1">
                    <i class="fa-solid fa-hourglass-half text-yellow-500"></i>
                    Menunggu pembayaran...
                </span>
            </div>

            <!-- Actions -->
            <button onclick="prosesPembayaranQRIS()" id="btn-proses-qris" 
                class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-3 mb-2 rounded-2xl font-extrabold hover:from-emerald-700 hover:to-teal-700 flex justify-center items-center gap-2 transition-all shadow-lg shadow-emerald-200/50 active:scale-[0.98]">
                <i class="fa-solid fa-rotate"></i> Cek Pembayaran
            </button>
            <button onclick="tutupQris()" class="w-full bg-gray-100 text-gray-500 py-2.5 rounded-2xl font-bold hover:bg-gray-200 transition-all active:scale-95">Batal</button>
        </div>
    </div>

    <!-- ====== MODAL SUKSES ====== -->
    <div id="success-modal" class="hidden fixed inset-0 bg-black/60 z-50 items-center justify-center p-4 modal-overlay">
        <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl text-center animate-scale-in">
            <!-- Animated Check -->
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 mb-4">
                <i class="fa-solid fa-circle-check text-emerald-500 text-5xl animate-bounce"></i>
            </div>
            <h3 class="text-2xl font-extrabold text-gray-900 mb-1">Pesanan Berhasil! 🎉</h3>
            <p class="text-sm text-gray-500 mb-5">Terima kasih, pesanan Anda sedang diproses.</p>
            
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-4 mb-5 text-sm border border-gray-100">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">ID Transaksi:</span>
                    <span id="customer-trx-id" class="font-bold text-gray-800"></span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-500">Total Dibayar:</span>
                    <span id="customer-total" class="font-extrabold text-emerald-600 text-lg"></span>
                </div>
            </div>

            <button onclick="selesaiPesan()" 
                class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-extrabold py-3.5 rounded-2xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-200/50 active:scale-[0.98]">
                <i class="fa-solid fa-check mr-2"></i> Selesai
            </button>
        </div>
    </div>

    <!-- ====== MODAL RIWAYAT PESANAN ====== -->
    <div id="riwayat-modal" class="hidden fixed inset-0 bg-black/60 z-50 items-center justify-center p-4 modal-overlay">
        <div class="bg-white rounded-3xl p-5 sm:p-6 max-w-2xl w-full shadow-2xl overflow-y-auto max-h-[85vh] animate-scale-in">
            <!-- Header -->
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-clock-rotate-left text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-gray-900">Riwayat Pesanan</h2>
                        <p class="text-[10px] text-gray-500">10 pesanan terakhir</p>
                    </div>
                </div>
                <button onclick="hideRiwayatModal()" class="w-8 h-8 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-all cursor-pointer">
                    <i class="fa-solid fa-xmark text-gray-500"></i>
                </button>
            </div>

            @if(count($riwayatPesanan) === 0)
                <div class="flex flex-col items-center justify-center py-12 text-gray-300">
                    <i class="fa-solid fa-ticket text-5xl mb-3"></i>
                    <p class="text-gray-400 font-medium">Belum ada pesanan</p>
                    <p class="text-xs text-gray-300 mt-1">Mulai pesan menu favoritmu sekarang!</p>
                </div>
            @else
                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    @foreach($riwayatPesanan as $rp)
                    <div onclick="showDetailPesanan('{{ $rp->id_transaksi }}')" class="bg-white border border-gray-100 rounded-2xl p-4 hover:shadow-md transition-all cursor-pointer hover:border-emerald-200 active:scale-[0.99]">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="font-extrabold text-gray-800 text-sm">
                                    <i class="fa-solid fa-receipt text-emerald-500 mr-1"></i>
                                    #{{ $rp->nomor_pesanan ?? $rp->id_transaksi }}
                                </span>
                                <span class="text-[10px] text-gray-400 ml-2">{{ $rp->created_at ? $rp->created_at->format('d/m/Y H:i') : '-' }}</span>
                            </div>
                            <span class="status-badge 
                                @if($rp->status_pembayaran === 'Lunas') bg-emerald-100 text-emerald-700
                                @else bg-red-100 text-red-700 @endif">
                                @if($rp->status_pembayaran === 'Lunas') <i class="fa-solid fa-circle-check"></i> Berhasil
                                @else <i class="fa-solid fa-spinner"></i> Proses @endif
                            </span>
                        </div>
                        
                        <div class="text-xs text-gray-500 space-y-1">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-box text-gray-300"></i>
                                <span>
                                    @foreach($rp->detailTransaksis as $dt)
                                        {{ $dt->produk->nama_produk ?? 'N/A' }} x{{ $dt->qty }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-wallet text-gray-300"></i>
                                <span class="font-bold text-gray-700">Rp {{ number_format($rp->total_bayar, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- ====== MODAL DETAIL PESANAN ====== -->
    <div id="detail-modal" class="hidden fixed inset-0 bg-black/60 z-50 items-center justify-center p-4 modal-overlay">
        <div class="bg-white rounded-3xl p-5 sm:p-6 max-w-lg w-full shadow-2xl overflow-y-auto max-h-[90vh] animate-scale-in">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-receipt text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-gray-900">Detail Pesanan</h3>
                    </div>
                </div>
                <button onclick="hideDetailPesanan()" class="w-8 h-8 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-all cursor-pointer">
                    <i class="fa-solid fa-xmark text-gray-500"></i>
                </button>
            </div>

            <!-- Info Pesanan -->
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-4 mb-4 text-sm border border-emerald-100 space-y-2.5">
                <div class="flex justify-between">
                    <span class="text-gray-500">ID Transaksi</span>
                    <span id="detail-id" class="font-bold text-gray-800 font-mono text-xs"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">No. Pesanan</span>
                    <span id="detail-nomor" class="font-bold text-gray-800"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal</span>
                    <span id="detail-tanggal" class="font-bold text-gray-800 text-xs"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Nama Customer</span>
                    <span id="detail-customer" class="font-bold text-gray-800"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Nomor HP</span>
                    <span id="detail-hp" class="font-bold text-gray-800"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Metode Bayar</span>
                    <span id="detail-metode" class="font-bold text-gray-800"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    <span id="detail-status-pembayaran" class="font-bold"></span>
                </div>
            </div>

            <!-- Daftar Item -->
            <div class="mb-4">
                <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-box text-emerald-500"></i> Item Pesanan
                </h4>
                <div id="detail-items" class="space-y-2 divide-y divide-gray-100 bg-gray-50 rounded-2xl p-3">
                </div>
            </div>

            <!-- Total -->
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-4 text-white mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-emerald-100 text-sm font-medium">Total Pembayaran</span>
                    <span id="detail-total" class="text-xl font-extrabold"></span>
                </div>
            </div>


            <button onclick="hideDetailPesanan()" class="w-full bg-gray-100 text-gray-600 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all active:scale-95">
                Tutup
            </button>
        </div>
    </div>

    <script>
        // ====== GLOBALS ======
        let keranjang = [];
        let bundlesData = @json($bundlesData);
        let riwayatData = @json($riwayatJson);
        let currentTrxId = '';
        let currentTotal = 0;

        function tambahBundleKeKeranjang(bundleId) {
            let bundle = bundlesData.find(b => b.id === bundleId);
            if (!bundle) { showToast('Bundle tidak ditemukan!', 'fa-circle-exclamation', 'text-red-400'); return; }

            bundle.items.forEach(item => {
                for (let i = 0; i < item.qty; i++) {
                    tambahKeKeranjang(item.id_produk, item.nama_produk, item.harga);
                }
            });
        }

        // ====== FORMAT RUPIAH ======
        function formatRupiah(angka) {
            return 'Rp ' + parseFloat(angka).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        // ====== TOAST ======
        function showToast(message, icon = 'fa-check-circle', color = 'text-emerald-400') {
            const toast = document.getElementById('toast');
            document.getElementById('toast-message').innerText = message;
            toast.querySelector('i').className = `fa-solid ${icon} ${color} mr-2`;
            toast.classList.add('show');
            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(() => toast.classList.remove('show'), 2500);
        }

        // ====== HANYA ANGKA ======
        function hanyaAngka(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }


        // ====== SEARCH PRODUK ======
        function setupSearch(inputId) {
            const searchInput = document.getElementById(inputId);
            if (!searchInput) return;
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const cards = document.querySelectorAll('.produk-card');
                let visibleCount = 0;
                cards.forEach(card => {
                    const nama = card.getAttribute('data-nama') || '';
                    if (nama.includes(query)) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                let noResult = document.getElementById('no-result-produk');
                if (visibleCount === 0 && query !== '') {
                    if (!noResult) {
                        noResult = document.createElement('p');
                        noResult.id = 'no-result-produk';
                        noResult.className = 'col-span-full text-gray-400 text-center py-8 text-sm';
                        noResult.innerHTML = '<i class="fa-solid fa-search mr-1"></i> Menu "<strong>' + this.value + '</strong>" tidak ditemukan';
                        document.getElementById('menu-produk').appendChild(noResult);
                    }
                } else if (noResult) {
                    noResult.remove();
                }
            });
        }

        // ====== CATEGORY PILLS ======
        function setupCategoryPills() {
            const pills = document.querySelectorAll('.category-pill');
            pills.forEach(pill => {
                pill.addEventListener('click', function() {
                    pills.forEach(p => p.classList.remove('active', 'bg-emerald-600', 'text-white'));
                    pills.forEach(p => { if (!p.classList.contains('active')) { p.classList.add('bg-gray-100', 'text-gray-600'); } });
                    this.classList.add('active', 'bg-emerald-600', 'text-white');
                    this.classList.remove('bg-gray-100', 'text-gray-600');
                    
                    const category = this.dataset.category;
                    const cards = document.querySelectorAll('.produk-card');
                    let visibleCount = 0;
                    cards.forEach(card => {
                        const cardKategori = card.getAttribute('data-kategori') || '';
                        if (category === 'all') {
                            card.style.display = '';
                            visibleCount++;
                        } else if (category === 'promo') {
                            const hasDiskon = card.querySelector('.bg-gradient-to-r.from-red-500');
                            if (hasDiskon) { card.style.display = ''; visibleCount++; }
                            else { card.style.display = 'none'; }
                        } else if (cardKategori === category) {
                            card.style.display = '';
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });
                    let noResult = document.getElementById('no-result-produk');
                    if (visibleCount === 0) {
                        if (!noResult) {
                            noResult = document.createElement('p');
                            noResult.id = 'no-result-produk';
                            noResult.className = 'col-span-full text-gray-400 text-center py-8 text-sm';
                            noResult.innerHTML = '<i class="fa-solid fa-search mr-1"></i> Tidak ada menu di kategori ini';
                            document.getElementById('menu-produk').appendChild(noResult);
                        }
                    } else if (noResult) { noResult.remove(); }
                });
            });
        }

        // ====== CART DRAWER (Mobile) ======
        function toggleCartDrawer() {
            const drawer = document.getElementById('cart-drawer');
            const overlay = document.getElementById('cart-overlay');
            if (drawer.classList.contains('open')) {
                drawer.classList.remove('open');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            } else {
                drawer.classList.add('open');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                updateTampilan(); // Refresh cart display
            }
        }

        // ====== KERANJANG ======
        function tambahKeKeranjang(id, nama, harga) {
            let index = keranjang.findIndex(item => item.id_produk === id);
            if (index !== -1) {
                keranjang[index].qty++;
            } else {
                keranjang.push({ id_produk: id, nama_produk: nama, harga: harga, qty: 1 });
            }
            updateTampilan();
            showToast(nama + ' ditambahkan ke pesanan!', 'fa-plus-circle', 'text-emerald-400');
            
            // Animasi floating cart button
            const floatBtn = document.getElementById('float-cart-btn');
            floatBtn.style.transform = 'scale(1.15)';
            setTimeout(() => { floatBtn.style.transform = ''; }, 200);
        }

        function kurangiDariKeranjang(id) {
            let index = keranjang.findIndex(item => item.id_produk === id);
            if (index !== -1) {
                if (keranjang[index].qty > 1) {
                    keranjang[index].qty--;
                } else {
                    keranjang.splice(index, 1);
                }
            }
            updateTampilan();
        }

        function updateTampilan() {
            // Desktop Cart
            const list = document.getElementById('keranjang-list');
            const total = document.getElementById('grand-total');
            const btn = document.getElementById('btn-pesan');
            
            // Mobile Cart
            const listMobile = document.getElementById('keranjang-list-mobile');
            const totalMobile = document.getElementById('mobile-grand-total');
            const btnMobile = document.getElementById('btn-pesan-mobile');

            // Badges
            const desktopCount = document.getElementById('desktop-cart-count');
            const mobileCount = document.getElementById('mobile-cart-count');
            const navBadge = document.getElementById('nav-cart-badge');
            const floatBadge = document.getElementById('cart-badge');

            const totalQty = keranjang.reduce((sum, item) => sum + item.qty, 0);

            // Update badges
            [desktopCount, mobileCount, navBadge, floatBadge].forEach(badge => {
                if (badge) {
                    if (totalQty > 0) {
                        badge.classList.remove('hidden');
                        badge.innerText = totalQty > 99 ? '99+' : totalQty;
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            });

            if (keranjang.length === 0) {
                const emptyHtml = '<div class="flex flex-col items-center justify-center py-10 text-gray-300"><i class="fa-solid fa-basket-shopping text-5xl mb-3"></i><p class="text-sm text-gray-400 font-medium">Belum ada pesanan</p><p class="text-xs text-gray-300 mt-1">Pilih menu untuk mulai pesan</p></div>';
                list.innerHTML = emptyHtml;
                if (listMobile) listMobile.innerHTML = emptyHtml;
                total.innerText = 'Rp 0';
                if (totalMobile) totalMobile.innerText = 'Rp 0';
                btn.disabled = true;
                if (btnMobile) btnMobile.disabled = true;
                return;
            }

            btn.disabled = false;
            if (btnMobile) btnMobile.disabled = false;
            
            let html = '';
            let grandTotal = 0;

            keranjang.forEach((item, i) => {
                let subtotal = item.harga * item.qty;
                grandTotal += subtotal;
                html += `<div class="flex justify-between items-center py-2.5 text-sm">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-gray-700 truncate">${item.nama_produk}</h4>
                        <p class="text-[11px] text-gray-400">${item.qty} x ${formatRupiah(item.harga)}</p>
                    </div>
                    <div class="flex items-center gap-2 ml-2">
                        <div class="flex items-center gap-1 bg-gray-50 rounded-xl px-1.5 py-1 border border-gray-100">
                            <button onclick="kurangiDariKeranjang('${item.id_produk}')" class="text-red-400 hover:text-red-600 w-6 h-6 flex items-center justify-center text-sm font-bold rounded-lg hover:bg-red-50 transition-all active:scale-90 cursor-pointer">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <span class="text-sm font-extrabold text-gray-800 min-w-[22px] text-center">${item.qty}</span>
                            <button onclick="tambahKeKeranjang('${item.id_produk}', '${item.nama_produk}', ${item.harga})" class="text-emerald-400 hover:text-emerald-600 w-6 h-6 flex items-center justify-center text-sm font-bold rounded-lg hover:bg-emerald-50 transition-all active:scale-90 cursor-pointer">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                        <span class="font-extrabold text-gray-700 min-w-[72px] text-right text-sm">${formatRupiah(subtotal)}</span>
                        <button onclick="hapusItem(${i})" class="text-red-300 hover:text-red-500 w-6 h-6 flex items-center justify-center text-xs rounded-lg hover:bg-red-50 transition-all active:scale-90 cursor-pointer">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>`;
            });

            list.innerHTML = html;
            if (listMobile) listMobile.innerHTML = html;
            total.innerText = formatRupiah(grandTotal);
            if (totalMobile) totalMobile.innerText = formatRupiah(grandTotal);
        }

        function hapusItem(index) {
            const item = keranjang[index];
            keranjang.splice(index, 1);
            updateTampilan();
            if (item) showToast(item.nama_produk + ' dihapus dari pesanan', 'fa-trash-can', 'text-red-400');
        }

        // ====== CHECKOUT MODAL ======
        function showCheckoutModal() {
            let grandTotal = keranjang.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            let orderNames = keranjang.map(item => item.nama_produk).join(', ');
            let orderNumber = '{{ $nomorPesananOtomatis }}';

            document.getElementById('checkout-nama-pesanan').innerText = orderNames;
            document.getElementById('checkout-total-harga').innerText = formatRupiah(grandTotal);
            document.getElementById('checkout-nomor-pesanan').innerText = orderNumber;

            let ringkasanHtml = '';
            keranjang.forEach(item => {
                let subtotal = item.harga * item.qty;
                ringkasanHtml += `<div class="flex justify-between py-1.5">
                    <span class="text-gray-600">${item.nama_produk} x${item.qty}</span>
                    <span class="font-bold text-gray-800">${formatRupiah(subtotal)}</span>
                </div>`;
            });
            document.getElementById('checkout-ringkasan-items').innerHTML = ringkasanHtml;

            document.getElementById('checkout-modal').classList.remove('hidden');
            document.getElementById('checkout-modal').classList.add('flex');

            setTimeout(function() {
                if (map) { map.invalidateSize(); }
            }, 300);
        }

        function hideCheckoutModal() {
            document.getElementById('checkout-modal').classList.add('hidden');
            document.getElementById('checkout-modal').classList.remove('flex');
        }

        function submitCheckout() {
            let namaPembeli = "{{ session('nama', 'Customer') }}";
            let nomor_hp = document.getElementById('nomor_hp').value.trim();
            let deskripsiPesanan = document.getElementById('deskripsi_pesanan').value.trim();
            let nomor_pesanan = document.getElementById('checkout-nomor-pesanan').innerText;

            if (!nomor_hp) { showToast('Nomor HP harus diisi!', 'fa-circle-exclamation', 'text-red-400'); return; }
            if (!/^[0-9]+$/.test(nomor_hp)) { showToast('Nomor HP hanya angka!', 'fa-circle-exclamation', 'text-red-400'); return; }

            fetch("{{ route('customer.transaksi.simpan') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    id_user: "{{ session('id_user') }}",
                    items: keranjang,
                    nama_pembeli: namaPembeli,
                    nomor_pesanan: nomor_pesanan,
                    nomor_hp: nomor_hp,
                    alamat: deskripsiPesanan || 'Tidak ada deskripsi',
                    detail_alamat: null,
                    latitude: null,
                    longitude: null
                })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal menyimpan pesanan.');
                return data;
            })
            .then(res => {
                if (res.status === 'success') {
                    currentTrxId = res.id_transaksi;
                    currentTotal = res.total_tagihan;
                    hideCheckoutModal();
                    tampilkanModalQRIS();
                } else {
                    showToast(res.message || 'Gagal menyimpan pesanan.', 'fa-circle-exclamation', 'text-red-400');
                }
            })
            .catch(err => {
                showToast(err.message || 'Terjadi kesalahan. Silakan coba lagi.', 'fa-circle-exclamation', 'text-red-400');
            });
        }

        function tampilkanModalQRIS() {
            let nomorPesanan = document.getElementById('checkout-nomor-pesanan').innerText;
            document.getElementById('qris-modal-total').innerText = formatRupiah(currentTotal);
            document.getElementById('qris-modal-total2').innerText = formatRupiah(currentTotal);
            document.getElementById('qris-nomor-pesanan').innerText = nomorPesanan;
            document.getElementById('qris-trx-id-display').innerText = currentTrxId;

            document.getElementById('qris-status-text').innerHTML = '<i class="fa-solid fa-hourglass-half text-yellow-500 mr-1"></i> Menunggu pembayaran...';

            let qrData = 'WARKOP-' + currentTrxId.substr(-8) + '-Rp' + parseInt(currentTotal);
            let qrImg = document.getElementById('qris-image');
            qrImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(qrData);
            qrImg.onerror = function() {
                this.src = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=TRX-' + currentTrxId.substr(-6);
            };

            document.getElementById('qrisModal').classList.remove('hidden');
            document.getElementById('qrisModal').classList.add('flex');
        }

        function tutupQris() {
            document.getElementById('qrisModal').classList.add('hidden');
            document.getElementById('qrisModal').classList.remove('flex');
        }

        function prosesPembayaranQRIS() {
            let btnProses = document.getElementById('btn-proses-qris');
            let qrisLoader = document.getElementById('qris-loader');
            let statusText = document.getElementById('qris-status-text');

            btnProses.disabled = true;
            btnProses.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memverifikasi...';
            qrisLoader.classList.remove('hidden');
            statusText.innerHTML = '<span class="text-blue-500"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Memverifikasi pembayaran...</span>';

            fetch("{{ route('customer.pembayaran.proses') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    id_transaksi: currentTrxId,
                    metode_bayar: 'QRIS',
                    nominal_bayar: currentTotal
                })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal memproses pembayaran.');
                return data;
            })
            .then(res => {
                btnProses.disabled = false;
                btnProses.innerHTML = '<i class="fa-solid fa-rotate"></i> Cek Pembayaran';
                qrisLoader.classList.add('hidden');

                if (res.status === 'success') {
                    statusText.innerHTML = '<span class="text-emerald-600 font-bold"><i class="fa-solid fa-circle-check mr-1"></i> Pembayaran Berhasil!</span>';
                    setTimeout(() => {
                        tutupQris();
                        document.getElementById('customer-trx-id').innerText = currentTrxId;
                        document.getElementById('customer-total').innerText = formatRupiah(currentTotal);
                        document.getElementById('success-modal').classList.remove('hidden');
                        document.getElementById('success-modal').classList.add('flex');
                    }, 1200);
                } else {
                    statusText.innerHTML = '<span class="text-red-500"><i class="fa-solid fa-circle-exclamation mr-1"></i> ' + (res.message || 'Pembayaran gagal') + '</span>';
                }
            })
            .catch(err => {
                btnProses.disabled = false;
                btnProses.innerHTML = '<i class="fa-solid fa-rotate"></i> Cek Pembayaran';
                qrisLoader.classList.add('hidden');
                statusText.innerHTML = '<span class="text-red-500"><i class="fa-solid fa-circle-exclamation mr-1"></i> ' + (err.message || 'Gagal cek pembayaran') + '</span>';
            });
        }

        function selesaiPesan() {
            keranjang = [];
            updateTampilan();
            document.getElementById('success-modal').classList.add('hidden');
            document.getElementById('success-modal').classList.remove('flex');
            showToast('Pesanan berhasil dibuat! 🎉', 'fa-circle-check', 'text-emerald-400');
        }

        // ====== DETAIL PESANAN ======
        function showDetailPesanan(idTransaksi) {
            const d = riwayatData.find(r => r.id_transaksi === idTransaksi);
            if (!d) {
                showToast('Data pesanan tidak ditemukan!', 'fa-circle-exclamation', 'text-red-400');
                return;
            }
            
            // Info dasar
            document.getElementById('detail-id').innerText = d.id_transaksi || '-';
            document.getElementById('detail-nomor').innerText = d.nomor_pesanan || '-';
            
            let tgl = d.created_at ? new Date(d.created_at).toLocaleString('id-ID', { 
                year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' 
            }) : '-';
            document.getElementById('detail-tanggal').innerText = tgl;
            document.getElementById('detail-customer').innerText = d.nama_pembeli || '-';
            document.getElementById('detail-hp').innerText = d.nomor_hp || '-';
            document.getElementById('detail-total').innerText = formatRupiah(d.total_bayar || 0);
            
            // Metode bayar
            document.getElementById('detail-metode').innerText = d.metode_bayar || '-';
            
            // Status pembayaran
            let statusBayarEl = document.getElementById('detail-status-pembayaran');
            if (d.status_pembayaran === 'Lunas') {
                statusBayarEl.innerHTML = '<span class="text-emerald-600"><i class="fa-solid fa-circle-check"></i> Berhasil</span>';
            } else {
                statusBayarEl.innerHTML = '<span class="text-red-600"><i class="fa-solid fa-spinner"></i> Proses</span>';
            }
            
            // Items
            let itemsHtml = '';
            if (d.items && d.items.length > 0) {
                d.items.forEach(item => {
                    itemsHtml += `<div class="flex justify-between items-center py-1.5 text-sm">
                        <div>
                            <span class="font-semibold text-gray-800">${item.nama_produk}</span>
                            <span class="text-gray-400 ml-1">x${item.qty}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-400">@${formatRupiah(item.harga_satuan)}</span>
                            <span class="font-bold text-gray-800 ml-2">${formatRupiah(item.subtotal)}</span>
                        </div>
                    </div>`;
                });
            }
            document.getElementById('detail-items').innerHTML = itemsHtml || '<p class="text-gray-400 text-sm">Tidak ada item</p>';

            // Tampilkan modal
            document.getElementById('detail-modal').classList.remove('hidden');
            document.getElementById('detail-modal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function hideDetailPesanan() {
            document.getElementById('detail-modal').classList.add('hidden');
            document.getElementById('detail-modal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        // ====== RIWAYAT MODAL ======
        function showRiwayatModal() {
            document.getElementById('riwayat-modal').classList.remove('hidden');
            document.getElementById('riwayat-modal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function hideRiwayatModal() {
            document.getElementById('riwayat-modal').classList.add('hidden');
            document.getElementById('riwayat-modal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        // ====== RESPONSIVE AUTO-ADJUSTMENT (No Refresh Needed) ======
        let isMobileView = window.innerWidth < 1024;
        let orientationChangeTimeout;

        function handleResponsiveChange() {
            const wasMobile = isMobileView;
            isMobileView = window.innerWidth < 1024;

            // Only act if viewport type actually changed
            if (wasMobile !== isMobileView) {
                // Close cart drawer if we switched to desktop
                const drawer = document.getElementById('cart-drawer');
                const overlay = document.getElementById('cart-overlay');
                if (!isMobileView && drawer.classList.contains('open')) {
                    drawer.classList.remove('open');
                    overlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }
                
                // Invalidate map to prevent rendering issues
                if (map) {
                    setTimeout(() => map.invalidateSize(), 300);
                }
            }

            // Always ensure map refreshes when becoming visible
            if (map) {
                setTimeout(() => map.invalidateSize(), 100);
            }
        }

        // Listen for viewport changes (resize, orientation change)
        window.addEventListener('resize', function() {
            clearTimeout(orientationChangeTimeout);
            orientationChangeTimeout = setTimeout(handleResponsiveChange, 150);
        });

        // Listen for fullscreen changes on mobile
        if (screen.orientation) {
            screen.orientation.addEventListener('change', function() {
                setTimeout(handleResponsiveChange, 400);
            });
        }

        // ====== DOM READY ======
        document.addEventListener('DOMContentLoaded', function() {
            setupSearch('search-produk');
            setupSearch('search-produk-mobile');
            setupCategoryPills();

            // Initial responsive check
            handleResponsiveChange();

            // Tutup modal saat klik di luar
            document.getElementById('riwayat-modal').addEventListener('click', function(e) {
                if (e.target === this) hideRiwayatModal();
            });
            document.getElementById('checkout-modal').addEventListener('click', function(e) {
                if (e.target === this) hideCheckoutModal();
            });
            document.getElementById('qrisModal').addEventListener('click', function(e) {
                if (e.target === this) tutupQris();
            });
            document.getElementById('success-modal').addEventListener('click', function(e) {
                if (e.target === this) selesaiPesan();
            });


            // Handle keyboard appearance on mobile (adjust viewport)
            const allInputs = document.querySelectorAll('input, textarea');
            allInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    // Scroll to keep input in view when keyboard opens on mobile
                    setTimeout(() => {
                        if (isMobileView) {
                            this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }, 300);
                });
            });

            console.log('✅ Warung Kopi Kita - Customer dashboard ready!');
        });
    </script>
</body>
</html>