<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kurir - Warung Kopi Kita</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        * { font-family: 'Inter', sans-serif; }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #f97316; border-radius: 10px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }

        .order-card { animation: fadeInUp 0.4s ease-out; transition: all 0.3s ease; }
        .order-card:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(0,0,0,0.08); }
        .order-card:active { transform: scale(0.99); }

        .sticky-header { position: sticky; top: 0; z-index: 30; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }

        .bottom-nav {
            display: none; position: fixed; bottom: 0; left: 0; right: 0; z-index: 35;
            background: white; border-top: 1px solid #e5e7eb; padding: 6px 0;
            padding-bottom: max(6px, env(safe-area-inset-bottom));
            box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
        }
        .bottom-nav a {
            flex: 1; display: flex; flex-direction: column; align-items: center; gap: 2px;
            font-size: 10px; color: #9ca3af; padding: 4px 0; text-decoration: none;
        }
        .bottom-nav a.active { color: #ea580c; }
        .bottom-nav a i { font-size: 20px; }

        .pulse-dot {
            width: 8px; height: 8px; background: #f97316; border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

        @media (max-width: 1023px) {
            .bottom-nav { display: flex !important; }
            body { padding-bottom: 64px; }
        }
        @media (min-width: 1024px) {
            .bottom-nav { display: none !important; }
            body { padding-bottom: 0; }
        }

        .toast {
            position: fixed; bottom: 80px; left: 50%; transform: translateX(-50%) translateY(20px);
            background: #1f2937; color: white; padding: 12px 24px; border-radius: 12px;
            font-size: 14px; font-weight: 600; box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            z-index: 100; opacity: 0; transition: all 0.3s ease; pointer-events: none; white-space: nowrap;
        }
        .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
    </style>
</head>
<body class="bg-gradient-to-br from-orange-50 via-white to-amber-50 min-h-screen">

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <i class="fa-solid fa-check-circle text-emerald-400 mr-2"></i>
        <span id="toast-message">Berhasil!</span>
    </div>

    <!-- ====== NAVBAR ====== -->
    <header class="sticky-header bg-white/90 shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-truck text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-extrabold text-gray-900 leading-tight">Dashboard Kurir</h1>
                        <p class="text-[10px] text-orange-600 font-semibold tracking-wide">WARUNG KOPI KITA</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 bg-orange-50 px-3 py-1.5 rounded-xl border border-orange-100">
                        <div class="w-7 h-7 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-user text-white text-xs"></i>
                        </div>
                        <span class="text-sm font-semibold text-orange-800 max-w-[100px] truncate">{{ session('nama', 'Kurir') }}</span>
                    </div>
                    <a href="{{ route('logout') }}" class="bg-red-50 hover:bg-red-100 text-red-500 p-2 rounded-xl transition-all" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-4 lg:py-6 pb-16 lg:pb-6">
        
        <!-- Header with count -->
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-list text-orange-500"></i>
                    Pesanan Untuk Dikirim
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    @if(count($pesanan) > 0)
                        {{ count($pesanan) }} pesanan {{ count($pesanan) > 1 ? 'siap diantar' : 'siap diantar' }}
                    @else
                        Semua pesanan sudah terkirim
                    @endif
                </p>
            </div>
            @if(count($pesanan) > 0)
            <div class="flex items-center gap-2 bg-orange-50 px-3 py-1.5 rounded-xl border border-orange-200">
                <span class="pulse-dot"></span>
                <span class="text-xs font-bold text-orange-700">{{ count($pesanan) }} Aktif</span>
            </div>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl mb-4 text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(count($pesanan) === 0)
            <div class="bg-white rounded-3xl p-10 shadow-sm border border-gray-100 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-check-circle text-emerald-500 text-4xl"></i>
                </div>
                <h3 class="text-lg font-extrabold text-gray-900 mb-1">Semua Terkirim! 🎉</h3>
                <p class="text-sm text-gray-500">Tidak ada pesanan yang perlu diantar saat ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($pesanan as $order)
                <div class="order-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Card Header -->
                    <div class="p-4 pb-2 border-b border-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="status-badge inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full
                                    {{ $order->status_pengiriman === 'dikirim' ? 'bg-orange-100 text-orange-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    @if($order->status_pengiriman === 'dikirim')
                                        <i class="fa-solid fa-truck"></i> Sedang Dikirim
                                    @else
                                        <i class="fa-solid fa-clock"></i> Menunggu
                                    @endif
                                </span>
                                <h3 class="font-extrabold text-gray-900 mt-2 text-base">{{ $order->nama_pembeli ?? 'Customer' }}</h3>
                            </div>
                            <span class="text-xs font-mono font-bold bg-gray-100 px-2.5 py-1 rounded-lg text-gray-700">{{ $order->nomor_pesanan ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 space-y-2 text-sm">
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot text-orange-400 mt-0.5"></i>
                            <span class="text-gray-600">{{ $order->alamat ? Str::limit($order->alamat, 60) : '-' }}</span>
                        </div>
                        @if($order->detail_alamat)
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-home text-orange-400 mt-0.5"></i>
                            <span class="text-gray-600">{{ $order->detail_alamat }}</span>
                        </div>
                        @endif
                        @if($order->latitude && $order->longitude)
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-map-pin text-orange-400"></i>
                            <a href="https://www.google.com/maps?q={{ $order->latitude }},{{ $order->longitude }}" 
                               target="_blank" class="text-blue-600 font-semibold hover:text-blue-700 transition-all">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] mr-1"></i> Buka Google Maps
                            </a>
                        </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-orange-400"></i>
                            <a href="tel:{{ $order->nomor_hp }}" class="text-blue-600 font-semibold hover:text-blue-700">{{ $order->nomor_hp ?? '-' }}</a>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-box text-orange-400 mt-1"></i>
                            <div class="text-gray-600">
                                @foreach($order->detailTransaksis as $dt)
                                    <span class="inline-block bg-gray-50 px-2 py-0.5 rounded-lg text-xs mr-1 mb-1">
                                        {{ $dt->produk->nama_produk ?? 'N/A' }} x{{ $dt->qty }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-extrabold text-emerald-600">Rp {{ number_format($order->total_bayar, 0, ',', '.') }}</span>
                            @if($order->status_pengiriman === 'menunggu')
                                <button onclick="konfirmasiKirim('{{ $order->id_transaksi }}')" class="bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white text-xs font-bold py-2 px-4 rounded-xl transition-all shadow-md shadow-orange-200/50 active:scale-95 cursor-pointer">
                                    <i class="fa-solid fa-truck mr-1"></i> Ambil & Antar
                                </button>
                            @else
                                <button onclick="konfirmasiSampai('{{ $order->id_transaksi }}')" class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white text-xs font-bold py-2 px-4 rounded-xl transition-all shadow-md shadow-emerald-200/50 active:scale-95 cursor-pointer">
                                    <i class="fa-solid fa-check-circle mr-1"></i> Konfirmasi Sampai
                                </button>
                            @endif
                        </div>
                        @if($order->status_pengiriman === 'dikirim')
                            <div class="mt-2 pt-2 border-t border-gray-200">
                                <div class="text-xs text-gray-500 mb-2 flex items-center gap-1">
                                    <i class="fa-solid fa-satellite-dish text-blue-500"></i>
                                    <span id="lokasi-text-{{ $order->id_transaksi }}">Lokasi belum dikirim</span>
                                </div>
                                <button onclick="kirimLokasi('{{ $order->id_transaksi }}')" id="btn-lokasi-{{ $order->id_transaksi }}" class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-xs font-bold py-2 rounded-xl transition-all shadow-md shadow-blue-200/50 active:scale-95 cursor-pointer">
                                    <i class="fa-solid fa-location-crosshairs mr-1"></i> Kirim Lokasi Saya
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </main>

    <!-- ====== BOTTOM NAV (Mobile) ====== -->
    <nav class="bottom-nav">
        <a href="#" class="active" onclick="event.preventDefault(); window.scrollTo({top: 0, behavior: 'smooth'});">
            <i class="fa-solid fa-home"></i><span>Beranda</span>
        </a>
        <a href="#" onclick="event.preventDefault(); window.scrollTo({top: 0, behavior: 'smooth'});">
            <i class="fa-solid fa-list"></i><span>Pesanan</span>
        </a>
        <a href="tel:{{ count($pesanan) > 0 && isset($pesanan[0]) ? ($pesanan[0]->nomor_hp ?? '#') : '#' }}" onclick="event.stopPropagation();">
            <i class="fa-solid fa-phone"></i><span>Hubungi</span>
        </a>
        <a href="{{ route('logout') }}">
            <i class="fa-solid fa-right-from-bracket"></i><span>Keluar</span>
        </a>
    </nav>

    <script>
        function showToast(message, icon = 'fa-check-circle', color = 'text-emerald-400') {
            const toast = document.getElementById('toast');
            document.getElementById('toast-message').innerText = message;
            toast.querySelector('i').className = `fa-solid ${icon} ${color} mr-2`;
            toast.classList.add('show');
            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(() => toast.classList.remove('show'), 3000);
        }

        function konfirmasiKirim(id) {
            if (!confirm('Ambil pesanan ini untuk diantar?')) return;
            const btn = event.target.closest('button');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
            
            fetch('/kurir/kirim/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast(res.message || 'Pesanan diambil! 🚚', 'fa-truck', 'text-orange-400');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast(res.message || 'Gagal!', 'fa-circle-exclamation', 'text-red-400');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-truck mr-1"></i> Ambil & Antar';
                }
            })
            .catch(() => {
                showToast('Terjadi kesalahan', 'fa-circle-exclamation', 'text-red-400');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-truck mr-1"></i> Ambil & Antar';
            });
        }

        function konfirmasiSampai(id) {
            if (!confirm('Konfirmasi pesanan ini sudah sampai ke customer?')) return;
            const btn = event.target.closest('button');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
            
            fetch('/kurir/sampai/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast('Pesanan telah sampai! ✅', 'fa-circle-check', 'text-emerald-400');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast(res.message || 'Gagal!', 'fa-circle-exclamation', 'text-red-400');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-check-circle mr-1"></i> Konfirmasi Sampai';
                }
            })
            .catch(() => {
                showToast('Terjadi kesalahan', 'fa-circle-exclamation', 'text-red-400');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check-circle mr-1"></i> Konfirmasi Sampai';
            });
        }

        function kirimLokasi(id) {
            if (!navigator.geolocation) {
                showToast('Browser tidak mendukung geolokasi.', 'fa-circle-exclamation', 'text-red-400');
                return;
            }

            const btn = document.getElementById('btn-lokasi-' + id);
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mendapatkan lokasi...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    fetch('/kurir/lokasi/' + id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ lat: lat, lng: lng })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            document.getElementById('lokasi-text-' + id).innerHTML =
                                '<a href="https://www.google.com/maps?q=' + lat + ',' + lng + '" target="_blank" class="text-blue-600 font-semibold underline">📍 Lihat di Maps</a>';
                            showToast('Lokasi berhasil dikirim! 📍', 'fa-location-crosshairs', 'text-blue-400');
                        } else {
                            showToast(res.message || 'Gagal mengirim lokasi.', 'fa-circle-exclamation', 'text-red-400');
                        }
                    })
                    .catch(function() {
                        showToast('Gagal mengirim lokasi.', 'fa-circle-exclamation', 'text-red-400');
                    })
                    .finally(function() {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-location-crosshairs mr-1"></i> Kirim Lokasi Saya';
                    });
                },
                function(error) {
                    showToast('Gagal dapat lokasi: ' + error.message, 'fa-circle-exclamation', 'text-red-400');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-location-crosshairs mr-1"></i> Kirim Lokasi Saya';
                },
                { enableHighAccuracy: true, timeout: 15000 }
            );
        }

        // Responsive handler
        document.addEventListener('DOMContentLoaded', function() {
            let isMobile = window.innerWidth < 1024;
            window.addEventListener('resize', function() {
                isMobile = window.innerWidth < 1024;
            });
        });
    </script>
</body>
</html>