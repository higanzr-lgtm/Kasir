<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kasir - Warung Kopi Kita</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        * { font-family: 'Inter', sans-serif; }
        
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        
        .product-card { animation: fadeInUp 0.4s ease-out; animation-fill-mode: both; }
        .product-card:nth-child(1) { animation-delay: 0.05s; }
        .product-card:nth-child(2) { animation-delay: 0.1s; }
        .product-card:nth-child(3) { animation-delay: 0.15s; }
        .product-card:nth-child(4) { animation-delay: 0.2s; }
        .product-card:nth-child(5) { animation-delay: 0.25s; }
        .product-card:nth-child(6) { animation-delay: 0.3s; }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 10px; }

        .sticky-header { position: sticky; top: 0; z-index: 30; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }

        .cart-drawer { position: fixed; bottom: 0; left: 0; right: 0; z-index: 50; transform: translateY(100%); transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 20px 20px 0 0; max-height: 85vh; }
        .cart-drawer.open { transform: translateY(0); }
        .drawer-handle { width: 36px; height: 4px; background: #d1d5db; border-radius: 4px; margin: 0 auto 12px; }

        .float-cart-btn { display: none; position: fixed; bottom: 24px; right: 24px; z-index: 40; box-shadow: 0 8px 32px rgba(59, 130, 246, 0.4); }
        .cart-badge { position: absolute; top: -6px; right: -6px; background: #ef4444; color: white; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; }

        .bottom-nav { display: none; position: fixed; bottom: 0; left: 0; right: 0; z-index: 35; background: white; border-top: 1px solid #e5e7eb; padding: 6px 0; padding-bottom: max(6px, env(safe-area-inset-bottom)); box-shadow: 0 -4px 20px rgba(0,0,0,0.08); }
        .bottom-nav a { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 2px; font-size: 10px; color: #9ca3af; padding: 4px 0; text-decoration: none; }
        .bottom-nav a.active { color: #2563eb; }
        .bottom-nav a i { font-size: 20px; }

        @media (max-width: 1023px) {
            .float-cart-btn { display: flex !important; }
            .bottom-nav { display: flex !important; }
            .desktop-cart { display: none !important; }
            body { padding-bottom: 64px; }
        }
        @media (min-width: 1024px) {
            .bottom-nav { display: none !important; }
            .float-cart-btn { display: none !important; }
            .desktop-cart { display: block !important; }
            body { padding-bottom: 0; }
        }
        
        @media print {
            body, body * { visibility: hidden; }
            #area-cetak-struk, #area-cetak-struk * { visibility: visible; }
            #area-cetak-struk {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 58mm !important;
                font-family: 'Courier New', Courier, monospace !important;
                font-size: 9pt !important;
                line-height: 1.2 !important;
                color: #000 !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                z-index: 9999 !important;
            }
            nav, .bottom-nav, .float-cart-btn, 
            #payment-modal, #success-modal, #qrisModal,
            .sticky-header, header, footer { 
                display: none !important; 
                visibility: hidden !important; 
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 min-h-screen">

    <header class="sticky-header bg-white/90 shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-cash-register text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-extrabold text-gray-900 leading-tight">Kasir POS</h1>
                        <p class="text-[10px] text-blue-600 font-semibold tracking-wide">WARUNG KOPI KITA</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="showRiwayatKasir()" class="hidden sm:flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-3.5 py-2 rounded-xl transition-all cursor-pointer">
                        <i class="fa-solid fa-clock-rotate-left text-blue-500"></i>
                        <span class="hidden md:inline">Riwayat</span>
                    </button>
                    <div class="flex items-center gap-2 bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100">
                        <div class="w-7 h-7 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-user text-white text-xs"></i>
                        </div>
                        <span class="text-sm font-semibold text-blue-800 max-w-[100px] truncate">{{ session('nama', 'Kasir') }}</span>
                    </div>
                    <a href="{{ route('logout') }}" class="bg-red-50 hover:bg-red-100 text-red-500 p-2 rounded-xl transition-all" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 pb-6 lg:py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
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

                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-boxes-stacked text-white text-sm"></i>
                        </div>
                        Daftar Produk
                    </h2>
                    <span class="text-xs text-gray-400">{{ count($produks) }} item</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-3 lg:gap-4">
                    @foreach($produks as $produk)
                    <div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all">
                        <div class="relative overflow-hidden aspect-[4/3] bg-gray-50">
                            <img src="{{ asset('images/menu/' . $produk->foto) }}" alt="{{ $produk->nama_produk }}" class="w-full h-full object-cover"
                                onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23e5e7eb%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2250%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%239ca3af%22 font-size=%2212%22>No Image</text></svg>'">
                            @if($produk->diskon)
                            <div class="absolute top-2 left-2 bg-gradient-to-r from-red-500 to-orange-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-md flex items-center gap-1">
                                <i class="fa-solid fa-bolt"></i> {{ $produk->diskon->nama_diskon }}
                            </div>
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="font-bold text-gray-800 text-sm line-clamp-2">{{ $produk->nama_produk }}</h3>
                            <p class="text-[10px] text-gray-400 mt-0.5">Stok: <span class="font-semibold {{ $produk->stok <= 5 ? 'text-red-500' : 'text-gray-700' }}">{{ $produk->stok }}</span></p>
                            @if($produk->diskon)
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="text-[10px] text-gray-400 line-through">Rp {{ number_format($produk->harga_normal, 0, ',', '.') }}</span>
                                    <span class="text-sm font-extrabold text-green-600">Rp {{ number_format($produk->getHargaNet(), 0, ',', '.') }}</span>
                                </div>
                            @else
                                <p class="text-sm font-extrabold text-gray-900 mt-1">Rp {{ number_format($produk->harga_normal, 0, ',', '.') }}</p>
                            @endif
                            <button onclick="tambahKeKeranjang('{{ $produk->id_produk }}', '{{ $produk->nama_produk }}', {{ $produk->getHargaNet() }})" 
                                class="mt-2 w-full bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-xs py-2.5 rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all font-bold shadow-md shadow-blue-200/50 active:scale-95 cursor-pointer flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="desktop-cart">
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 lg:sticky lg:top-20">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-shopping-cart text-white text-sm"></i>
                            </div>
                            Keranjang
                        </h2>
                        <span id="desktop-cart-count" class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full hidden">0</span>
                    </div>
                    <div id="keranjang-list" class="space-y-2 divide-y divide-gray-100 max-h-60 overflow-y-auto pr-1">
                        <div class="flex flex-col items-center justify-center py-10 text-gray-300">
                            <i class="fa-solid fa-basket-shopping text-5xl mb-3"></i>
                            <p class="text-sm text-gray-400 font-medium">Keranjang kosong</p>
                        </div>
                    </div>
                    <form id="form-data-pembeli" class="border-t pt-4 mt-2 space-y-3" onsubmit="return false;">
                        <h3 class="text-sm font-bold text-gray-700"><i class="fa-solid fa-user text-blue-500 mr-1"></i> Data Pembeli</h3>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-0.5">Nama Pembeli</label>
                            <input type="text" id="nama_pembeli" class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-200 transition-all outline-none" placeholder="Nama pembeli...">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-0.5">No. Pesanan</label>
                            <input type="text" id="nomor_pesanan" class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm bg-gray-50 text-gray-500 outline-none" value="{{ $nomorPesananOtomatis }}" readonly>
                        </div>
                    </form>
                    <div class="border-t pt-4 mt-2">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-500 text-sm font-medium">Total Tagihan</span>
                            <span id="grand-total" class="text-2xl font-extrabold text-blue-600">Rp 0</span>
                        </div>
                        <button onclick="prosesCheckout()" id="btn-checkout" 
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-extrabold py-3 rounded-2xl hover:from-green-600 hover:to-emerald-700 transition-all disabled:from-gray-200 disabled:to-gray-300 disabled:text-gray-400 disabled:cursor-not-allowed shadow-lg shadow-green-200/50 active:scale-[0.98]" disabled>
                            <i class="fa-solid fa-file-invoice-dollar mr-2"></i> Simpan Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <button onclick="toggleCartDrawer()" id="float-cart-btn" class="float-cart-btn bg-gradient-to-r from-blue-500 to-indigo-500 text-white w-14 h-14 rounded-2xl items-center justify-center shadow-xl cursor-pointer">
        <i class="fa-solid fa-bag-shopping text-xl"></i>
        <span id="cart-badge" class="cart-badge hidden">0</span>
    </button>

    <div id="cart-overlay" class="hidden fixed inset-0 bg-black/40 z-40 lg:hidden" onclick="toggleCartDrawer()"></div>
    <div id="cart-drawer" class="cart-drawer bg-white shadow-2xl lg:hidden">
        <div class="p-5 pb-2">
            <div class="drawer-handle"></div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-shopping-cart text-blue-500"></i> Keranjang
                </h2>
                <span id="mobile-cart-count" class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full hidden">0</span>
            </div>
            <input type="text" id="nama_pembeli_mobile" class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm mb-2 focus:border-blue-400 outline-none" placeholder="Nama pembeli...">
        </div>
        <div id="keranjang-list-mobile" class="px-5 space-y-2 max-h-[30vh] overflow-y-auto flex-1">
            <div class="flex flex-col items-center justify-center py-8 text-gray-300">
                <i class="fa-solid fa-basket-shopping text-4xl mb-3"></i>
                <p class="text-sm text-gray-400 font-medium">Keranjang kosong</p>
            </div>
        </div>
        <div class="p-5 border-t border-gray-100">
            <div class="flex justify-between items-center mb-3">
                <span class="text-gray-500 text-sm font-medium">Total</span>
                <span id="mobile-grand-total" class="text-xl font-extrabold text-blue-600">Rp 0</span>
            </div>
            <div class="flex gap-2">
                <button onclick="toggleCartDrawer()" class="flex-1 bg-gray-100 text-gray-600 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all active:scale-95">Lanjut</button>
                <button onclick="prosesCheckout(); toggleCartDrawer();" id="btn-checkout-mobile" class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-extrabold py-3 rounded-2xl hover:from-green-600 hover:to-emerald-700 transition-all disabled:from-gray-200 disabled:to-gray-300 disabled:text-gray-400 disabled:cursor-not-allowed shadow-lg shadow-green-200/50 active:scale-[0.98]" disabled>
                    <i class="fa-solid fa-file-invoice-dollar mr-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="#" class="active" onclick="event.preventDefault(); window.scrollTo({top: 0, behavior: 'smooth'});">
            <i class="fa-solid fa-store"></i><span>Produk</span>
        </a>
        <a href="#" onclick="event.preventDefault(); toggleCartDrawer();" class="relative">
            <i class="fa-solid fa-bag-shopping"></i><span>Keranjang</span>
            <span id="nav-cart-badge" class="absolute -top-0.5 right-1/2 translate-x-6 bg-red-500 text-white text-[8px] font-bold w-4 h-4 rounded-full flex items-center justify-center hidden">0</span>
        </a>
        <a href="{{ route('logout') }}"><i class="fa-solid fa-right-from-bracket"></i><span>Keluar</span></a>
    </nav>

    <!-- MODAL PEMBAYARAN -->
    <div id="payment-modal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-3xl p-5 sm:p-6 max-w-sm w-full shadow-2xl animate-scale-in">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-money-bill-wave text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-gray-900">Pembayaran</h3>
                    <p class="text-xs text-gray-500">Konfirmasi pembayaran transaksi</p>
                </div>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-4 mb-4 space-y-2 border border-blue-100">
                <p class="text-sm text-gray-600 flex justify-between"><span>ID Transaksi:</span><span id="modal-trx-id" class="font-bold text-gray-800 font-mono"></span></p>
                <p class="text-sm text-gray-600 flex justify-between"><span>Total Bayar:</span><span id="modal-trx-total" class="font-extrabold text-blue-600 text-lg"></span></p>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5"><i class="fa-solid fa-credit-card text-green-500 mr-1"></i> Metode Pembayaran</label>
                    <select id="metode_bayar" class="w-full border-2 border-gray-200 rounded-2xl p-3 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-200 outline-none" onchange="toggleNominalBayar()">
                        <option value="Tunai">💵 Uang Tunai</option>
                        <option value="QRIS">📱 Digital QRIS</option>
                    </select>
                </div>
                <div id="div_nominal_bayar">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5"><i class="fa-solid fa-cash text-green-500 mr-1"></i> Nominal Uang Diterima</label>
                    <input type="number" id="nominal_bayar" class="w-full border-2 border-gray-200 rounded-2xl p-3 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-200 outline-none" placeholder="Masukkan jumlah uang...">
                </div>
            </div>
            <div class="mt-5 flex gap-3">
                <button onclick="document.getElementById('payment-modal').classList.add('hidden'); document.getElementById('payment-modal').classList.remove('flex');" class="flex-1 bg-gray-100 text-gray-600 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all active:scale-95">Batal</button>
                <button onclick="bayarSekarang()" class="flex-[2] bg-gradient-to-r from-green-600 to-emerald-600 text-white font-extrabold py-3 rounded-2xl hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg shadow-green-200/50 active:scale-[0.98]">
                    <i class="fa-solid fa-check mr-1"></i> Konfirmasi Lunas
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL QRIS -->
    <div id="qrisModal" class="hidden fixed inset-0 bg-black/60 z-50 items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-2xl w-full max-w-sm text-center animate-scale-in">
            <div class="flex items-center justify-center gap-2 mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center shadow-md">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/02/QRIS_logo.svg/1200px-QRIS_logo.svg.png" alt="QRIS" class="h-5 brightness-0 invert">
                </div>
                <h2 class="text-lg font-extrabold text-gray-900">Pembayaran QRIS</h2>
            </div>
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-4 mb-3 text-sm border border-emerald-100">
                <div class="flex justify-between"><span class="text-gray-500">Total Bayar:</span><span id="qris-modal-total" class="font-extrabold text-emerald-600 text-lg"></span></div>
                <div class="flex justify-between mt-1.5"><span class="text-gray-500">Merchant:</span><span class="font-bold text-gray-800">Warung Kopi Kita</span></div>
            </div>
            <div class="bg-white border-2 border-dashed border-emerald-200 rounded-2xl p-3 mb-3 relative mx-auto max-w-[200px]">
                <div id="qris-loader" class="hidden absolute inset-0 flex items-center justify-center bg-white/80 rounded-2xl z-10">
                    <i class="fa-solid fa-spinner fa-spin text-3xl text-emerald-500"></i>
                </div>
                <img id="qris-image" src="" alt="QR Code" class="w-full h-auto mx-auto">
            </div>
            <div class="bg-blue-50 rounded-2xl p-3 mb-3 text-xs text-blue-700 text-left">
                <p class="font-bold mb-1"><i class="fa-solid fa-circle-info"></i> Cara Bayar:</p>
                <ol class="space-y-0.5">
                    <li>1. Buka Gojek / OVO / Dana / M-Banking</li>
                    <li>2. Pilih Bayar → QRIS</li>
                    <li>3. Scan QR Code & bayar</li>
                    <li>4. Klik "Cek Pembayaran"</li>
                </ol>
            </div>
            <div id="qris-status-area" class="text-xs text-center mb-3">
                <span id="qris-status-text" class="text-gray-400"><i class="fa-solid fa-hourglass-half text-yellow-500 mr-1"></i> Menunggu...</span>
            </div>
            <button onclick="prosesPembayaranQRIS()" id="btn-proses-qris" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-3 mb-2 rounded-2xl font-extrabold hover:from-emerald-700 hover:to-teal-700 shadow-lg shadow-emerald-200/50 active:scale-[0.98]">
                <i class="fa-solid fa-rotate"></i> Cek Pembayaran
            </button>
            <button onclick="tutupQris()" class="w-full bg-gray-100 text-gray-500 py-2.5 rounded-2xl font-bold hover:bg-gray-200 active:scale-95">Batal</button>
        </div>
    </div>

    <!-- ====== MODAL RIWAYAT TRANSAKSI ====== -->
    <div id="riwayat-kasir-modal" class="hidden fixed inset-0 bg-black/60 z-50 items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-3xl p-5 sm:p-6 max-w-2xl w-full shadow-2xl overflow-y-auto max-h-[85vh] animate-scale-in">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-clock-rotate-left text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-gray-900">Riwayat Transaksi</h2>
                        <p class="text-[10px] text-gray-500">20 transaksi terakhir</p>
                    </div>
                </div>
                <button onclick="hideRiwayatKasir()" class="w-8 h-8 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-all cursor-pointer">
                    <i class="fa-solid fa-xmark text-gray-500"></i>
                </button>
            </div>
            @if(count($riwayatTransaksi) === 0)
                <div class="flex flex-col items-center justify-center py-12 text-gray-300">
                    <i class="fa-solid fa-receipt text-5xl mb-3"></i>
                    <p class="text-gray-400 font-medium">Belum ada transaksi</p>
                </div>
            @else
                <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                    @foreach($riwayatTransaksi as $rt)
                    <div onclick="showDetailRiwayatKasir('{{ $rt->id_transaksi }}')" class="bg-white border border-gray-100 rounded-2xl p-3 hover:shadow-md transition-all cursor-pointer hover:border-blue-200 active:scale-[0.99]">
                        <div class="flex justify-between items-start">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-lg">{{ $rt->id_transaksi }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $rt->created_at ? $rt->created_at->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                                <p class="text-xs text-gray-700 mt-1 font-semibold truncate">{{ $rt->nama_pembeli ?? '-' }}</p>
                                <div class="text-[10px] text-gray-500 mt-0.5">
                                    @foreach($rt->detailTransaksis as $dt)
                                        {{ $dt->produk->nama_produk ?? 'N/A' }} x{{ $dt->qty }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </div>
                            </div>
                            <div class="text-right ml-2">
                                <p class="font-extrabold text-blue-600 text-sm">Rp {{ number_format($rt->total_bayar, 0, ',', '.') }}</p>
                                <span class="text-[9px] px-2 py-0.5 rounded-full font-semibold 
                                    @if($rt->status_pembayaran === 'Lunas') bg-emerald-100 text-emerald-700
                                    @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ $rt->status_pembayaran === 'Lunas' ? 'Lunas' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-2 pt-2 border-t border-gray-50">
                            <span class="text-[9px] text-gray-400"><i class="fa-solid fa-credit-card mr-1"></i>{{ $rt->pembayaran->metode_bayar ?? '-' }}</span>
                            @if($rt->nomor_pesanan)
                            <span class="text-[9px] text-gray-400"><i class="fa-solid fa-hashtag mr-1"></i>{{ $rt->nomor_pesanan }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
            <button onclick="hideRiwayatKasir()" class="mt-4 w-full bg-gray-100 text-gray-600 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all active:scale-95">Tutup</button>
        </div>
    </div>

    <!-- ====== MODAL DETAIL RIWAYAT KASIR ====== -->
    <div id="detail-kasir-modal" class="hidden fixed inset-0 bg-black/60 z-50 items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-3xl p-5 sm:p-6 max-w-lg w-full shadow-2xl overflow-y-auto max-h-[90vh] animate-scale-in">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-receipt text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-gray-900">Detail Transaksi</h3>
                        <p id="detail-kasir-status" class="text-[10px] font-semibold"></p>
                    </div>
                </div>
                <button onclick="hideDetailRiwayatKasir()" class="w-8 h-8 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-all cursor-pointer">
                    <i class="fa-solid fa-xmark text-gray-500"></i>
                </button>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-4 mb-4 text-sm border border-blue-100 space-y-2.5">
                <div class="flex justify-between"><span class="text-gray-500">ID Transaksi</span><span id="detail-kasir-id" class="font-bold text-gray-800 font-mono text-xs"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">No. Pesanan</span><span id="detail-kasir-nomor" class="font-bold text-gray-800"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Tanggal</span><span id="detail-kasir-tanggal" class="font-bold text-gray-800 text-xs"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Pembeli</span><span id="detail-kasir-pembeli" class="font-bold text-gray-800"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Metode Bayar</span><span id="detail-kasir-metode" class="font-bold text-gray-800"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status</span><span id="detail-kasir-statusbayar" class="font-bold"></span></div>
            </div>
            <div class="mb-4">
                <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-box text-blue-500"></i> Item Transaksi</h4>
                <div id="detail-kasir-items" class="space-y-2 divide-y divide-gray-100 bg-gray-50 rounded-2xl p-3"></div>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-4 text-white mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-blue-100 text-sm font-medium">Total Pembayaran</span>
                    <span id="detail-kasir-total" class="text-xl font-extrabold"></span>
                </div>
            </div>
            <button onclick="hideDetailRiwayatKasir()" class="w-full bg-gray-100 text-gray-600 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all active:scale-95">Tutup</button>
        </div>
    </div>

    <!-- MODAL SUKSES -->
    <div id="success-modal" class="hidden fixed inset-0 bg-black/60 z-50 items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl text-center animate-scale-in">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-gradient-to-br from-green-100 to-emerald-100 mb-4">
                <i class="fa-solid fa-circle-check text-green-500 text-5xl"></i>
            </div>
            <h3 class="text-2xl font-extrabold text-gray-900 mb-1">Pembayaran Berhasil! 🎉</h3>
            <p class="text-sm text-gray-500 mb-5">Transaksi telah dicatat ke dalam sistem.</p>
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-4 mb-5 text-sm border border-gray-100">
                <div id="pop-sukses-pembeli-row" class="flex justify-between text-xs text-gray-500" style="display: none;"><span>Pembeli:</span><span id="pop-sukses-pembeli" class="font-bold text-gray-800">-</span></div>
                <div class="flex justify-between text-xs text-gray-500 mt-1"><span>Tanggal:</span><span id="pop-sukses-tanggal" class="font-bold text-gray-800">-</span></div>
                <div class="flex justify-between text-xs text-gray-500 mt-1"><span>Metode Bayar:</span><span id="pop-sukses-metode" class="font-bold text-gray-800">-</span></div>
                <div class="flex justify-between text-xs text-gray-500 mt-1"><span>Total Tagihan:</span><span id="pop-sukses-total" class="font-bold text-gray-800">-</span></div>
                <div id="pop-sukses-diterima-row" class="flex justify-between text-xs text-gray-500 mt-1"><span>Uang Diterima:</span><span id="pop-sukses-diterima" class="font-bold text-gray-800">-</span></div>
                <div id="pop-sukses-kembalian-row" class="flex justify-between text-sm pt-2 border-t border-dashed border-gray-200 mt-2"><span class="font-semibold text-gray-700">Uang Kembalian:</span><span id="pop-sukses-kembalian" class="font-black text-green-600 text-lg">-</span></div>
            </div>
            <button onclick="eksekusiCetakStruk()" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-extrabold py-3.5 rounded-2xl hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg shadow-green-200/50 active:scale-[0.98] flex items-center justify-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Struk & Selesai
            </button>
        </div>
    </div>

    <!-- AREA CETAK STRUK -->
    <div id="area-cetak-struk" style="display: none;">
        <div style="text-align: center; margin-bottom: 10px;">
            <h3 style="margin: 0; font-size: 12pt; font-weight: bold;">WARUNG KOPI KITA</h3>
            <p style="margin: 2px 0; font-size: 8pt;">Jl. Anggrek No. 12, Bogor</p>
            <p style="margin: 2px 0; font-size: 8pt;">Telp: 0812-3456-7890</p>
            <p style="margin: 5px 0;">================================</p>
        </div>
        <div style="font-size: 8pt; margin-bottom: 8px;">
            <p style="margin: 2px 0;" id="struk-tanggal">Tgl: -</p>
            <p style="margin: 2px 0;" id="struk-id">Nota: -</p>
            <p style="margin: 2px 0; display: none;" id="struk-pembeli">Pembeli: -</p>
            <p style="margin: 2px 0;" id="struk-kasir">Kasir: {{ session('nama', 'Kasir') }}</p>
            <p style="margin: 5px 0;">--------------------------------</p>
        </div>
        <div id="struk-items" style="font-size: 9pt;"></div>
        <div style="font-size: 9pt; margin-top: 5px;">
            <p style="margin: 5px 0;">--------------------------------</p>
            <div style="display: flex; justify-content: space-between;"><span>Total Tagihan:</span><span id="struk-total">Rp 0</span></div>
            <div id="struk-bayar-row" style="display: flex; justify-content: space-between;"><span>Nominal Bayar:</span><span id="struk-bayar">Rp 0</span></div>
            <div id="struk-kembalian-row" style="display: flex; justify-content: space-between; font-weight: bold;"><span>Kembalian:</span><span id="struk-kembalian">Rp 0</span></div>
            <p style="margin: 5px 0;">================================</p>
        </div>
        <div style="text-align: center; margin-top: 15px; font-size: 8pt;">
            <p style="margin: 2px 0; font-weight: bold;">TERIMA KASIH</p>
            <p style="margin: 2px 0;">Selamat Menikmati Kembali</p>
        </div>
    </div>

    <script>
        let keranjang = [];
        let bundlesData = @json($bundlesData);
        let riwayatKasirData = @json($riwayatTransaksiJson);

        function tambahBundleKeKeranjang(bundleId) {
            let bundle = bundlesData.find(b => b.id === bundleId);
            if (!bundle) { alert('Bundle tidak ditemukan!'); return; }
            bundle.items.forEach(item => {
                for (let i = 0; i < item.qty; i++) {
                    tambahKeKeranjang(item.id_produk, item.nama_produk, item.harga);
                }
            });
            const floatBtn = document.getElementById('float-cart-btn');
            floatBtn.style.transform = 'scale(1.2)';
            setTimeout(() => { floatBtn.style.transform = ''; }, 300);
        }

        function formatRupiah(angka) {
            return 'Rp ' + parseFloat(angka).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        function tambahKeKeranjang(id, nama, harga) {
            let index = keranjang.findIndex(item => item.id_produk === id);
            if (index !== -1) { keranjang[index].qty++; }
            else { keranjang.push({ id_produk: id, nama_produk: nama, harga: harga, qty: 1, diskon_persen: 0 }); }
            updateTampilanKeranjang();
            const floatBtn = document.getElementById('float-cart-btn');
            floatBtn.style.transform = 'scale(1.15)';
            setTimeout(() => { floatBtn.style.transform = ''; }, 200);
        }

        function kurangiDariKeranjang(id) {
            let index = keranjang.findIndex(item => item.id_produk === id);
            if (index !== -1) {
                if (keranjang[index].qty > 1) keranjang[index].qty--;
                else keranjang.splice(index, 1);
            }
            updateTampilanKeranjang();
        }

        function updateTampilanKeranjang() {
            const list = document.getElementById('keranjang-list');
            const total = document.getElementById('grand-total');
            const btn = document.getElementById('btn-checkout');
            const listMobile = document.getElementById('keranjang-list-mobile');
            const totalMobile = document.getElementById('mobile-grand-total');
            const btnMobile = document.getElementById('btn-checkout-mobile');
            const totalQty = keranjang.reduce((sum, item) => sum + item.qty, 0);
            ['desktop-cart-count', 'mobile-cart-count', 'nav-cart-badge', 'cart-badge'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    if (totalQty > 0) { el.classList.remove('hidden'); el.innerText = totalQty > 99 ? '99+' : totalQty; }
                    else { el.classList.add('hidden'); }
                }
            });
            if (keranjang.length === 0) {
                const emptyHtml = '<div class="flex flex-col items-center justify-center py-8 text-gray-300"><i class="fa-solid fa-basket-shopping text-5xl mb-3"></i><p class="text-sm text-gray-400 font-medium">Keranjang kosong</p></div>';
                list.innerHTML = emptyHtml;
                if (listMobile) listMobile.innerHTML = emptyHtml;
                total.innerText = 'Rp 0'; if (totalMobile) totalMobile.innerText = 'Rp 0';
                btn.disabled = true; if (btnMobile) btnMobile.disabled = true;
                return;
            }
            btn.disabled = false; if (btnMobile) btnMobile.disabled = false;
            let html = ''; let grandTotal = 0;
            keranjang.forEach((item, i) => {
                let subtotal = item.harga * item.qty;
                let diskonItem = item.diskon_persen || 0;
                let potongan = subtotal * (diskonItem / 100);
                let subFinal = subtotal - potongan;
                grandTotal += subFinal;
                html += `<div class="flex justify-between items-start py-2 text-sm border-b border-gray-50">
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center">
                            <h4 class="font-bold text-gray-700 truncate">${item.nama_produk}</h4>
                            <span class="font-extrabold text-gray-600 ml-2">${formatRupiah(subFinal)}</span>
                        </div>
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <div class="flex items-center gap-1 bg-gray-50 rounded-xl px-1.5 py-1 border border-gray-100">
                                <button onclick="kurangiDariKeranjang('${item.id_produk}')" class="text-red-400 hover:text-red-600 w-6 h-6 flex items-center justify-center text-sm font-bold rounded-lg hover:bg-red-50 cursor-pointer"><i class="fa-solid fa-minus"></i></button>
                                <span class="text-sm font-extrabold text-gray-800 min-w-[20px] text-center">${item.qty}</span>
                                <button onclick="tambahKeKeranjang('${item.id_produk}', '${item.nama_produk}', ${item.harga})" class="text-emerald-400 hover:text-emerald-600 w-6 h-6 flex items-center justify-center text-sm font-bold rounded-lg hover:bg-emerald-50 cursor-pointer"><i class="fa-solid fa-plus"></i></button>
                            </div>
                            <input type="number" min="0" max="100" step="0.1" value="${diskonItem}" class="w-14 border-2 border-yellow-200 rounded-lg p-0.5 text-xs text-center text-yellow-700 bg-yellow-50 focus:border-yellow-400 outline-none" onchange="ubahDiskonItem(${i}, this.value)" oninput="ubahDiskonItem(${i}, this.value)">
                            <span class="text-[9px] text-yellow-600 font-bold">%</span>
                            ${potongan > 0 ? `<span class="text-[9px] text-red-400">(-${formatRupiah(potongan)})</span>` : ''}
                        </div>
                    </div>
                </div>`;
            });
            list.innerHTML = html; if (listMobile) listMobile.innerHTML = html;
            total.innerText = formatRupiah(grandTotal); if (totalMobile) totalMobile.innerText = formatRupiah(grandTotal);
        }

        function ubahDiskonItem(index, value) {
            let diskon = parseFloat(value) || 0;
            if (diskon < 0) diskon = 0;
            if (diskon > 100) diskon = 100;
            keranjang[index].diskon_persen = diskon;
            updateTampilanKeranjang();
        }

        // ====== RIWAYAT KASIR ======
        function showDetailRiwayatKasir(idTransaksi) {
            const d = riwayatKasirData.find(r => r.id_transaksi === idTransaksi);
            if (!d) { alert('Data tidak ditemukan!'); return; }
            document.getElementById('detail-kasir-id').innerText = d.id_transaksi || '-';
            document.getElementById('detail-kasir-nomor').innerText = d.nomor_pesanan || '-';
            let tgl = d.created_at ? new Date(d.created_at).toLocaleString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-';
            document.getElementById('detail-kasir-tanggal').innerText = tgl;
            document.getElementById('detail-kasir-pembeli').innerText = d.nama_pembeli || '-';
            document.getElementById('detail-kasir-metode').innerText = d.metode_bayar || '-';
            document.getElementById('detail-kasir-total').innerText = formatRupiah(d.total_bayar || 0);
            let statusEl = document.getElementById('detail-kasir-statusbayar');
            let statusSub = document.getElementById('detail-kasir-status');
            if (d.status_pembayaran === 'Lunas') {
                statusEl.innerHTML = '<span class="text-emerald-600"><i class="fa-solid fa-circle-check"></i> Berhasil</span>';
                statusSub.innerHTML = '<span class="text-emerald-600">✅ Pembayaran lunas</span>';
            } else {
                statusEl.innerHTML = '<span class="text-yellow-600"><i class="fa-solid fa-spinner"></i> Pending</span>';
                statusSub.innerHTML = '<span class="text-yellow-600">⏳ Belum dibayar</span>';
            }
            let itemsHtml = '';
            if (d.items && d.items.length > 0) {
                d.items.forEach(item => {
                    itemsHtml += `<div class="flex justify-between items-center py-1.5 text-sm">
                        <div><span class="font-semibold text-gray-800">${item.nama_produk}</span><span class="text-gray-400 ml-1">x${item.qty}</span></div>
                        <div class="text-right"><span class="text-xs text-gray-400">@${formatRupiah(item.harga_satuan)}</span><span class="font-bold text-gray-800 ml-2">${formatRupiah(item.subtotal)}</span></div>
                    </div>`;
                });
            }
            document.getElementById('detail-kasir-items').innerHTML = itemsHtml || '<p class="text-gray-400 text-sm">Tidak ada item</p>';
            document.getElementById('detail-kasir-modal').classList.remove('hidden');
            document.getElementById('detail-kasir-modal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function hideDetailRiwayatKasir() {
            document.getElementById('detail-kasir-modal').classList.add('hidden');
            document.getElementById('detail-kasir-modal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        function showRiwayatKasir() {
            document.getElementById('riwayat-kasir-modal').classList.remove('hidden');
            document.getElementById('riwayat-kasir-modal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function hideRiwayatKasir() {
            document.getElementById('riwayat-kasir-modal').classList.add('hidden');
            document.getElementById('riwayat-kasir-modal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('click', function(e) {
            const m1 = document.getElementById('riwayat-kasir-modal');
            const m2 = document.getElementById('detail-kasir-modal');
            if (e.target === m1) hideRiwayatKasir();
            if (e.target === m2) hideDetailRiwayatKasir();
        });

        let currentTrxId = ''; let currentTotal = 0;
        let strukDataCache = null;

        function toggleCartDrawer() {
            const drawer = document.getElementById('cart-drawer');
            const overlay = document.getElementById('cart-overlay');
            if (drawer.classList.contains('open')) {
                drawer.classList.remove('open'); overlay.classList.add('hidden');
                document.body.style.overflow = '';
            } else {
                drawer.classList.add('open'); overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; updateTampilanKeranjang();
                document.getElementById('nama_pembeli_mobile').value = document.getElementById('nama_pembeli').value;
            }
        }

        function toggleNominalBayar() {
            document.getElementById('div_nominal_bayar').classList.toggle('hidden', document.getElementById('metode_bayar').value === 'QRIS');
        }

        function prosesCheckout() {
            let namaPembeli = window.innerWidth < 1024 ? document.getElementById('nama_pembeli_mobile').value.trim() : document.getElementById('nama_pembeli').value.trim();
            let nomorPesanan = document.getElementById('nomor_pesanan').value.trim();
            fetch("{{ route('transaksi.simpan') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify({ id_user: "{{ session('id_user', 2) }}", items: keranjang, nama_pembeli: namaPembeli || null, nomor_pesanan: nomorPesanan || null })
            })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    currentTrxId = res.id_transaksi;
                    currentTotal = res.total_tagihan;
                    document.getElementById('modal-trx-id').innerText = currentTrxId;
                    document.getElementById('modal-trx-total').innerText = formatRupiah(currentTotal);
                    document.getElementById('metode_bayar').value = 'Tunai';
                    document.getElementById('nominal_bayar').value = '';
                    toggleNominalBayar();
                    document.getElementById('payment-modal').classList.remove('hidden');
                    document.getElementById('payment-modal').classList.add('flex');
                } else { alert(res.message); }
            });
        }

        function isiDataStruk(dataTrx) {
            let tanggalRealtime = new Date(dataTrx.created_at).toLocaleString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('struk-id').innerText = "Nota: " + (dataTrx.id_transaksi || '-');
            document.getElementById('struk-tanggal').innerText = "Tgl : " + tanggalRealtime;
            document.getElementById('struk-total').innerText = formatRupiah(dataTrx.total_bayar || 0);
            if (dataTrx.pembayaran) {
                document.getElementById('struk-bayar').innerText = formatRupiah(dataTrx.pembayaran.nominal_bayar || 0);
                document.getElementById('struk-kembalian').innerText = formatRupiah(dataTrx.pembayaran.kembalian || 0);
                document.getElementById('struk-bayar-row').style.display = 'flex';
                document.getElementById('struk-kembalian-row').style.display = 'flex';
            } else {
                document.getElementById('struk-bayar-row').style.display = 'none';
                document.getElementById('struk-kembalian-row').style.display = 'none';
            }
            let infoPembeli = '';
            if (dataTrx.nama_pembeli) infoPembeli += 'Pembeli: ' + dataTrx.nama_pembeli;
            if (dataTrx.nomor_pesanan) infoPembeli += (infoPembeli ? ' | ' : '') + 'No: ' + dataTrx.nomor_pesanan;
            if (infoPembeli) { document.getElementById('struk-pembeli').innerText = infoPembeli; document.getElementById('struk-pembeli').style.display = 'block'; }
            else { document.getElementById('struk-pembeli').style.display = 'none'; }
            let htmlItems = '';
            if (dataTrx.detail_transaksis) {
                dataTrx.detail_transaksis.forEach(item => {
                    let subtotal = parseFloat(item.subtotal) || 0;
                    let namaProduk = item.produk?.nama_produk || 'N/A';
                    let hargaSatuan = item.qty > 0 ? item.subtotal / item.qty : 0;
                    htmlItems += `<div style="margin-bottom: 5px;"><div style="font-weight: bold;">${namaProduk}</div><div style="display: flex; justify-content: space-between; font-size: 8pt;"><span>${item.qty} x ${formatRupiah(hargaSatuan)}</span><span>${formatRupiah(subtotal)}</span></div></div>`;
                });
            }
            document.getElementById('struk-items').innerHTML = htmlItems;
            strukDataCache = dataTrx;
        }

        function bayarSekarang() {
            let nominal = document.getElementById('nominal_bayar').value;
            let metode = document.getElementById('metode_bayar').value;
            if (metode === 'QRIS') {
                document.getElementById('qris-modal-total').innerText = formatRupiah(currentTotal);
                document.getElementById('qris-status-text').innerHTML = '<i class="fa-solid fa-hourglass-half text-yellow-500 mr-1"></i> Menunggu pembayaran...';
                let qrData = 'WARKOPKITA-' + currentTrxId + '-Rp' + currentTotal;
                document.getElementById('qris-image').src = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(qrData);
                document.getElementById('qris-image').onerror = function() { this.src = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=WARKOPKITA-' + currentTrxId; };
                document.getElementById('payment-modal').classList.add('hidden');
                document.getElementById('payment-modal').classList.remove('flex');
                document.getElementById('qrisModal').classList.remove('hidden');
                document.getElementById('qrisModal').classList.add('flex');
                return;
            }
            if(!nominal || parseFloat(nominal) < currentTotal) { alert("Nominal uang yang diterima kurang atau belum diisi!"); return; }
            fetch("{{ route('pembayaran.proses') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify({ id_transaksi: currentTrxId, metode_bayar: metode, nominal_bayar: nominal })
            })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    let dataTrx = res.data;
                    isiDataStruk(dataTrx);
                    document.getElementById('pop-sukses-tanggal').innerText = new Date(dataTrx.created_at).toLocaleString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    document.getElementById('pop-sukses-metode').innerText = dataTrx.pembayaran.metode_bayar;
                    document.getElementById('pop-sukses-total').innerText = formatRupiah(dataTrx.total_bayar);
                    document.getElementById('pop-sukses-diterima').innerText = formatRupiah(dataTrx.pembayaran.nominal_bayar);
                    document.getElementById('pop-sukses-diterima-row').style.display = 'flex';
                    document.getElementById('pop-sukses-kembalian').innerText = formatRupiah(dataTrx.pembayaran.kembalian);
                    document.getElementById('pop-sukses-kembalian-row').style.display = 'flex';
                    document.getElementById('payment-modal').classList.remove('flex');
                    document.getElementById('payment-modal').classList.add('hidden');
                    document.getElementById('success-modal').classList.remove('hidden');
                    document.getElementById('success-modal').classList.add('flex');
                    confetti({ particleCount: 150, spread: 80, origin: { y: 0.6 } });
                } else { alert(res.message); }
            });
        }

        function eksekusiCetakStruk() {
            const strukId = document.getElementById('struk-id').innerText;
            if (strukId === 'Nota: -' || !strukId) { alert('Data struk belum tersedia!'); return; }
            document.getElementById('success-modal').classList.remove('flex');
            document.getElementById('success-modal').classList.add('hidden');
            const printArea = document.getElementById('area-cetak-struk');
            printArea.style.display = 'block';
            setTimeout(() => { window.print(); }, 500);
        }

        if (window.matchMedia) {
            const mediaQueryList = window.matchMedia('print');
            mediaQueryList.addListener(function(mql) {
                if (!mql.matches) { setTimeout(() => { document.getElementById('area-cetak-struk').style.display = 'none'; location.reload(); }, 500); }
            });
        }
        window.onafterprint = function() { setTimeout(() => { document.getElementById('area-cetak-struk').style.display = 'none'; location.reload(); }, 500); };

        function tutupQris() { document.getElementById('qrisModal').classList.add('hidden'); document.getElementById('qrisModal').classList.remove('flex'); }

        function prosesPembayaranQRIS() {
            let btnProses = document.getElementById('btn-proses-qris');
            let qrisLoader = document.getElementById('qris-loader');
            btnProses.disabled = true;
            btnProses.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
            qrisLoader.classList.remove('hidden');
            setTimeout(() => {
                fetch("{{ route('pembayaran.proses') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify({ id_transaksi: currentTrxId, metode_bayar: 'QRIS', nominal_bayar: currentTotal })
                })
                .then(res => res.json())
                .then(res => {
                    btnProses.disabled = false;
                    btnProses.innerHTML = '<i class="fa-solid fa-rotate"></i> Cek Pembayaran';
                    qrisLoader.classList.add('hidden');
                    if(res.status === 'success') {
                        tutupQris();
                        let dataTrx = res.data;
                        isiDataStruk(dataTrx);
                        document.getElementById('struk-bayar-row').style.display = 'none';
                        document.getElementById('struk-kembalian-row').style.display = 'none';
                        document.getElementById('pop-sukses-tanggal').innerText = new Date(dataTrx.created_at).toLocaleString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
                        document.getElementById('pop-sukses-metode').innerText = 'QRIS';
                        document.getElementById('pop-sukses-total').innerText = formatRupiah(dataTrx.total_bayar);
                        document.getElementById('pop-sukses-diterima').innerText = formatRupiah(dataTrx.total_bayar);
                        document.getElementById('pop-sukses-diterima-row').style.display = 'flex';
                        document.getElementById('pop-sukses-kembalian-row').style.display = 'none';
                        document.getElementById('payment-modal').classList.remove('flex');
                        document.getElementById('payment-modal').classList.add('hidden');
                        document.getElementById('success-modal').classList.remove('hidden');
                        document.getElementById('success-modal').classList.add('flex');
                        confetti({ particleCount: 200, spread: 100, origin: { y: 0.5 }, colors: ['#26ccff', '#a25afd', '#ff5e7e', '#88ff5a', '#fcff42', '#ffa62d', '#ff36ff'] });
                    } else { alert(res.message); }
                });
            }, 1500);
        }

        let isMobileView = window.innerWidth < 1024;
        window.addEventListener('resize', function() {
            const was = isMobileView;
            isMobileView = window.innerWidth < 1024;
            if (was !== isMobileView) {
                const drawer = document.getElementById('cart-drawer');
                const overlay = document.getElementById('cart-overlay');
                if (!isMobileView && drawer.classList.contains('open')) { drawer.classList.remove('open'); overlay.classList.add('hidden'); document.body.style.overflow = ''; }
            }
        });
    </script>
</body>
</html>