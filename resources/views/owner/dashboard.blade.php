<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Owner - Warung Kopi Kita</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        * { font-family: 'Inter', sans-serif; }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #8b5cf6; border-radius: 10px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }

        .sticky-header { position: sticky; top: 0; z-index: 30; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
        .animate-scale-in { animation: scaleIn 0.3s ease-out; }

        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.1); }

        .tab-active { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); }
        .tab-inactive { background: #f3f4f6; color: #374151; }
        .tab-inactive:hover { background: #e5e7eb; }

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
        .bottom-nav a.active { color: #7c3aed; }
        .bottom-nav a i { font-size: 20px; }

        @media (max-width: 1023px) {
            .bottom-nav { display: flex !important; }
            body { padding-bottom: 64px; }
            .desktop-tabs { overflow-x: auto; flex-wrap: nowrap !important; }
            .desktop-tabs button { white-space: nowrap; font-size: 11px !important; padding: 8px 12px !important; }
        }
        @media (min-width: 1024px) {
            .bottom-nav { display: none !important; }
            body { padding-bottom: 0; }
        }

        @media print {
            body * { visibility: hidden; }
            #area-cetak-struk-owner, #area-cetak-struk-owner * { visibility: visible; }
            #area-cetak-struk-owner {
                position: absolute; left: 0; top: 0; width: 50mm;
                font-family: 'Courier New', Courier, monospace; font-size: 10pt; line-height: 1.2; color: #000;
            }
        }

        .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-scroll table { min-width: 600px; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-white to-purple-50 min-h-screen">

    <!-- ====== NAVBAR ====== -->
    <header class="sticky-header bg-white/90 shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-user-gear text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-extrabold text-gray-900 leading-tight">Panel Owner</h1>
                        <p class="text-[10px] text-purple-600 font-semibold tracking-wide">MANAJEMEN TOKO</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 bg-purple-50 px-3 py-1.5 rounded-xl border border-purple-100">
                        <div class="w-7 h-7 bg-gradient-to-br from-indigo-400 to-purple-600 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-crown text-white text-xs"></i>
                        </div>
                        <span class="text-sm font-semibold text-purple-800 max-w-[100px] truncate">{{ session('nama', 'Owner') }}</span>
                    </div>
                    <a href="{{ route('logout') }}" class="bg-red-50 hover:bg-red-100 text-red-500 p-2 rounded-xl transition-all" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-4 lg:py-6 space-y-6 pb-16 lg:pb-6">
        
        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="stat-card bg-gradient-to-r from-blue-500 to-blue-600 p-5 rounded-2xl shadow-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-blue-100 font-semibold uppercase tracking-wide">Total Omset</p>
                        <h3 class="text-2xl font-black mt-1">Rp {{ number_format($totalOmsetSistem, 0, ',', '.') }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-wallet text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-gradient-to-r from-green-500 to-emerald-600 p-5 rounded-2xl shadow-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-green-100 font-semibold uppercase tracking-wide">Total Transaksi</p>
                        <h3 class="text-2xl font-black mt-1">{{ $totalTransaksiSistem }} Kali</h3>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-receipt text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card bg-gradient-to-r from-purple-500 to-purple-600 p-5 rounded-2xl shadow-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-purple-100 font-semibold uppercase tracking-wide">Status Database</p>
                        <h3 class="text-xl font-bold mt-1">Sinkron & Aman</h3>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-database text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="desktop-tabs flex gap-1.5 border-b border-gray-200 pb-2 overflow-x-auto">
            <button onclick="switchTab('tab-grafik')" id="btn-tab-grafik" class="tab-active px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5">
                <i class="fa-solid fa-chart-line"></i> Grafik
            </button>
            <button onclick="switchTab('tab-produk')" id="btn-tab-produk" class="tab-inactive px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle"></i> Produk
            </button>
            <button onclick="switchTab('tab-inventaris')" id="btn-tab-inventaris" class="tab-inactive px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5">
                <i class="fa-solid fa-boxes-stacked"></i> Stok
            </button>
            <button onclick="switchTab('tab-promo')" id="btn-tab-promo" class="tab-inactive px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5">
                <i class="fa-solid fa-tags"></i> Promo
            </button>
            <button onclick="switchTab('tab-transaksi')" id="btn-tab-transaksi" class="tab-inactive px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5">
                <i class="fa-solid fa-receipt"></i> Transaksi
            </button>
            <button onclick="switchTab('tab-pesanan')" id="btn-tab-pesanan" class="tab-inactive px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5">
                <i class="fa-solid fa-truck"></i> Pesanan ({{ count($pesananCustomer) }})
            </button>
            <button onclick="switchTab('tab-user')" id="btn-tab-user" class="tab-inactive px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5">
                <i class="fa-solid fa-users-gear"></i> User
            </button>
            <button onclick="switchTab('tab-bundle')" id="btn-tab-bundle" class="tab-inactive px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5">
                <i class="fa-solid fa-gift"></i> Bundle
            </button>
        </div>

        <!-- Tab: Grafik Omset -->
        <div id="tab-grafik" class="tab-content animate-fade-in-up">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-base font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-blue-500"></i> Tren Omset (7 Hari)
                </h2>
                <div class="w-full h-64">
                    <canvas id="chartOmsetHarian"></canvas>
                </div>
            </div>
        </div>

        <!-- Tab: Tambah Produk -->
        <div id="tab-produk" class="tab-content hidden">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 max-w-lg mx-auto">
                <h2 class="text-base font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-blue-500"></i> Tambah Produk Baru
                </h2>
                
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">{{ session('error') }}</div>
                @endif

                <form action="{{ route('owner.produk.tambah') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                            <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif
                    @csrf                    
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Kode Produk</label>
                        <input type="text" name="id_produk" required class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-200 outline-none" placeholder="Contoh: P004">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Nama Produk</label>
                        <input type="text" name="nama_produk" required class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-200 outline-none" placeholder="Nama makanan / minuman">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Harga Jual</label>
                            <input type="number" name="harga_normal" required class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-blue-400 outline-none" placeholder="Rp">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Stok</label>
                            <input type="number" name="stok" required class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-blue-400 outline-none" placeholder="Pcs">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Diskon (Opsional)</label>
                        <select name="id_diskon" class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-blue-400 outline-none bg-white">
                            <option value="">-- Tidak Ada Diskon --</option>
                            @foreach($diskons as $diskon)
                            <option value="{{ $diskon->id_diskon }}">{{ $diskon->nama_diskon }} ({{ $diskon->nilai }}%)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Foto Produk</label>
                        <input type="file" name="foto" required class="w-full border-2 border-gray-200 rounded-xl p-2 text-sm" accept="image/*">
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-extrabold py-3 rounded-2xl hover:from-blue-600 hover:to-indigo-700 transition-all shadow-lg shadow-blue-200/50 active:scale-[0.98]">
                        <i class="fa-solid fa-save mr-1"></i> Simpan Produk
                    </button>
                </form>
            </div>
        </div>

        <!-- Tab: Inventaris & Stok -->
        <div id="tab-inventaris" class="tab-content hidden">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-base font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-yellow-500"></i> Data Inventaris & Stok
                </h2>
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
                @endif
                <div class="table-scroll">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-xs uppercase">
                                <th class="p-3 border-b font-bold">ID</th>
                                <th class="p-3 border-b font-bold">Nama</th>
                                <th class="p-3 border-b font-bold">Harga</th>
                                <th class="p-3 border-b font-bold">Stok</th>
                                <th class="p-3 border-b font-bold">Diskon</th>
                                <th class="p-3 border-b font-bold text-center">Edit</th>
                                <th class="p-3 border-b font-bold text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($produks as $p)
                            <tr class="hover:bg-gray-50 text-gray-600">
                                <td class="p-3 font-mono font-bold text-xs">{{ $p->id_produk }}</td>
                                <td class="p-3 font-semibold text-gray-800">{{ $p->nama_produk }}</td>
                                <td class="p-3">Rp {{ number_format($p->harga_normal, 0, ',', '.') }}</td>
                                <td class="p-3">
                                    @if($p->stok <= 10)
                                        <span class="text-red-600 font-bold bg-red-50 px-2.5 py-1 rounded-lg text-xs border border-red-200"><i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ $p->stok }}</span>
                                    @else
                                        <span class="text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg text-xs font-medium">{{ $p->stok }} Pcs</span>
                                    @endif
                                </td>
                                <td class="p-3 text-xs">{{ $p->diskon ? $p->diskon->nama_diskon : 'Kosong' }}</td>
                                <td class="p-3 text-center">
                                    <button onclick="showEditBarang('{{ $p->id_produk }}', '{{ $p->nama_produk }}', {{ $p->harga_normal }}, {{ $p->stok }}, '{{ $p->id_diskon ?? '' }}')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-sm cursor-pointer">
                                        <i class="fa-solid fa-pen mr-1"></i> Edit
                                    </button>
                                </td>
                                <td class="p-3 text-center">
                                    <form action="{{ route('owner.produk.hapus', $p->id_produk) }}" method="POST" onsubmit="return confirm('Yakin hapus produk ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 px-2.5 py-1.5 rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 transition text-xs font-bold">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab: Tambah Promo -->
        <div id="tab-promo" class="tab-content hidden">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 max-w-lg mx-auto">
                <h2 class="text-base font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-tags text-pink-500"></i> Tambah Promo / Diskon
                </h2>
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
                @endif
                <form action="{{ route('owner.promo.tambah') }}" method="POST" class="space-y-4">
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                            <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Kode Diskon</label>
                        <input type="text" name="id_diskon" required class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-pink-400 outline-none" placeholder="DISC20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Nama Promo</label>
                        <input type="text" name="nama_diskon" required class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-pink-400 outline-none" placeholder="Promo Akhir Bulan 20%">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Tipe</label>
                            <select name="tipe_diskon" required class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-pink-400 outline-none bg-white">
                                <option value="Persen">Persen (%)</option>
                                <option value="Nominal">Nominal (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Nilai</label>
                            <input type="number" name="nilai" required class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-pink-400 outline-none" placeholder="10 / 5000">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Mulai</label>
                            <input type="date" name="tgl_mulai" required class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-pink-400 outline-none" value="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Selesai</label>
                            <input type="date" name="tgl_selesai" required class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-pink-400 outline-none" value="{{ date('Y-m-d', strtotime('+1 month')) }}">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-pink-500 to-rose-600 text-white font-extrabold py-3 rounded-2xl hover:from-pink-600 hover:to-rose-700 transition-all shadow-lg shadow-pink-200/50 active:scale-[0.98]">
                        <i class="fa-solid fa-tag mr-1"></i> Tambah Promo
                    </button>
                </form>

                <!-- Daftar Promo -->
                <div class="mt-6">
                    <h3 class="font-bold text-gray-700 mb-3 flex items-center gap-2 text-sm"><i class="fa-solid fa-list"></i> Promo Aktif</h3>
                    @if(count($diskons) === 0)
                        <p class="text-gray-400 text-center py-4 text-sm">Belum ada promo.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($diskons as $d)
                            <div class="flex justify-between items-center bg-gray-50 rounded-2xl p-3 text-sm border border-gray-100">
                                <div>
                                    <span class="font-bold text-gray-800">{{ $d->nama_diskon }}</span>
                                    <span class="text-xs text-gray-400 ml-2">({{ $d->id_diskon }})</span>
                                </div>
                                <div class="text-right">
                                    <span class="bg-pink-100 text-pink-700 px-3 py-1 rounded-xl text-xs font-bold">
                                        @if($d->tipe_diskon === 'Persen') {{ $d->nilai }}%
                                        @else Rp {{ number_format($d->nilai, 0, ',', '.') }}
                                        @endif
                                    </span>
                                    <p class="text-[9px] text-gray-400 mt-0.5">{{ date('d/m/Y', strtotime($d->tgl_mulai)) }} - {{ date('d/m/Y', strtotime($d->tgl_selesai)) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modal Edit Barang -->
        <div id="modal-edit-barang" class="hidden fixed inset-0 bg-black/60 z-50 items-center justify-center p-4">
            <div class="bg-white rounded-3xl p-5 sm:p-6 max-w-lg w-full shadow-2xl animate-scale-in">
                <h3 class="text-lg font-extrabold text-gray-900 mb-4 flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-yellow-500"></i> Edit Barang</h3>
                <form id="form-edit-barang" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="edit-barang-id" name="id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Nama Produk</label>
                            <input type="text" name="nama_produk" id="edit-nama" required class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-yellow-400 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">Harga (Rp)</label>
                                <input type="number" name="harga_normal" id="edit-harga" required min="0" class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-yellow-400 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">Stok</label>
                                <input type="number" name="stok" id="edit-stok" required min="0" class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-yellow-400 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Diskon</label>
                            <select name="id_diskon" id="edit-diskon" class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-yellow-400 outline-none bg-white">
                                <option value="">-- Tidak Ada --</option>
                                @foreach($diskons as $diskon)
                                <option value="{{ $diskon->id_diskon }}">{{ $diskon->nama_diskon }} ({{ $diskon->nilai }}%)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Foto (biarkan kosong)</label>
                            <input type="file" name="foto" class="w-full border-2 border-gray-200 rounded-xl p-2 text-sm" accept="image/*">
                        </div>
                    </div>
                    <div class="mt-5 flex gap-3">
                        <button type="button" onclick="hideEditBarang()" class="flex-1 bg-gray-100 text-gray-600 font-bold py-2.5 rounded-2xl hover:bg-gray-200 transition">Batal</button>
                        <button type="submit" class="flex-[2] bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-extrabold py-2.5 rounded-2xl hover:from-yellow-600 hover:to-orange-600 transition shadow-lg shadow-yellow-200/50">
                            <i class="fa-solid fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab: Data Transaksi -->
        <div id="tab-transaksi" class="tab-content hidden">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-base font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-green-500"></i> Data Transaksi
                </h2>
                <div class="table-scroll">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-xs uppercase">
                                <th class="p-3 border-b font-bold">ID</th>
                                <th class="p-3 border-b font-bold">Tgl</th>
                                <th class="p-3 border-b font-bold">Pembeli</th>
                                <th class="p-3 border-b font-bold">No. HP</th>
                                <th class="p-3 border-b font-bold">No.</th>
                                <th class="p-3 border-b font-bold">Item</th>
                                <th class="p-3 border-b font-bold">Total</th>
                                <th class="p-3 border-b font-bold">Metode</th>
                                <th class="p-3 border-b font-bold text-center">Struk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($semuaTransaksi as $trx)
                            <tr class="hover:bg-gray-50 text-gray-600">
                                <td class="p-3 font-mono font-bold text-xs">{{ $trx->id_transaksi }}</td>
                                <td class="p-3 text-xs">{{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i') : '-' }}</td>
                                <td class="p-3 font-semibold">{{ $trx->nama_pembeli ?? '-' }}</td>
                                <td class="p-3 text-xs font-mono">{{ $trx->nomor_hp ?? '-' }}</td>
                                <td class="p-3">{{ $trx->nomor_pesanan ?? '-' }}</td>
                                <td class="p-3 text-xs">
                                    @foreach($trx->detailTransaksis as $dt)
                                        {{ $dt->produk->nama_produk ?? 'N/A' }} x{{ $dt->qty }}<br>
                                    @endforeach
                                </td>
                                <td class="p-3 font-bold">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                                <td class="p-3">
                                    @if($trx->pembayaran)
                                        <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg text-xs font-medium border border-blue-200">{{ $trx->pembayaran->metode_bayar }}</span>
                                    @else <span class="text-gray-400">-</span> @endif
                                </td>
                                <td class="p-3 text-center">
                                    <button onclick="cetakStrukOwner('{{ $trx->id_transaksi }}')" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer">
                                        <i class="fa-solid fa-print mr-1"></i> Cetak
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="p-6 text-center text-gray-400">Belum ada transaksi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab: Pesanan Customer -->
        <div id="tab-pesanan" class="tab-content hidden">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-base font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-truck text-orange-500"></i> Pesanan Customer
                </h2>
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
                @endif

                @if(count($pesananCustomer) === 0)
                    <p class="text-gray-400 text-center py-6">Belum ada pesanan customer.</p>
                @else
                    <div class="table-scroll">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 text-xs uppercase">
                                <th class="p-3 border-b font-bold">No.</th>
                                <th class="p-3 border-b font-bold">Customer</th>
                                <th class="p-3 border-b font-bold">HP</th>
                                <th class="p-3 border-b font-bold">Total</th>
                                <th class="p-3 border-b font-bold">Status</th>
                                <th class="p-3 border-b font-bold text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($pesananCustomer as $psn)
                                <tr class="hover:bg-gray-50 text-gray-600">
                                    <td class="p-3 font-mono font-bold">{{ $psn->nomor_pesanan ?? '-' }}</td>
                                    <td class="p-3 font-semibold">{{ $psn->nama_pembeli ?? '-' }}</td>
                                    <td class="p-3">{{ $psn->nomor_hp ?? '-' }}</td>
                                    <td class="p-3 font-bold">Rp {{ number_format($psn->total_bayar, 0, ',', '.') }}</td>
                                    <td class="p-3">
                                        @if($psn->status_pembayaran === 'Lunas')
                                            <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-lg text-xs font-medium border border-emerald-200"><i class="fa-solid fa-circle-check mr-1"></i> Berhasil</span>
                                        @else
                                            <span class="bg-red-50 text-red-700 px-2.5 py-1 rounded-lg text-xs font-medium border border-red-200"><i class="fa-solid fa-spinner mr-1"></i> Proses</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center">
                                        <button onclick="showOwnerDetailPesanan('{{ $psn->id_transaksi }}')" class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-sm cursor-pointer">
                                            <i class="fa-solid fa-eye mr-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab: Bundle / Paket Menu -->
        <div id="tab-bundle" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Form Tambah Bundle -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="text-base font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-gift text-orange-500"></i> Tambah Bundle/Paket
                    </h2>
                    <form id="form-tambah-bundle" action="{{ route('owner.bundle.tambah') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Nama Bundle</label>
                            <input type="text" name="nama_bundle" required class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-orange-400 outline-none" placeholder="Paket Hemat 1">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">Harga Bundle (Rp)</label>
                                <input type="number" name="harga_bundle" required min="0" class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-orange-400 outline-none" placeholder="25000">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">Aktif?</label>
                                <select name="aktif" class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-orange-400 outline-none bg-white">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Deskripsi</label>
                            <textarea name="deskripsi" rows="2" class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:border-orange-400 outline-none" placeholder="Isi paket: 1 Kopi + 1 Roti Bakar"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Foto Bundle (Opsional)</label>
                            <input type="file" name="foto" class="w-full border-2 border-gray-200 rounded-xl p-2 text-sm" accept="image/*">
                        </div>

                        <div class="border-t pt-4">
                            <h3 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                                <i class="fa-solid fa-list-check text-orange-500"></i> Item Produk dalam Bundle
                            </h3>
                            <div id="bundle-items-container" class="space-y-2">
                                <div class="bundle-item flex gap-2 items-center">
                                    <select name="items[0][id_produk]" required class="flex-1 border-2 border-gray-200 rounded-xl p-2 text-sm outline-none focus:border-orange-400 bg-white">
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($produks as $prod)
                                        <option value="{{ $prod->id_produk }}">{{ $prod->nama_produk }} (Rp {{ number_format($prod->harga_normal, 0, ',', '.') }})</option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="items[0][qty]" value="1" min="1" class="w-16 border-2 border-gray-200 rounded-xl p-2 text-sm text-center outline-none focus:border-orange-400">
                                    <button type="button" onclick="hapusBundleItem(this)" class="text-red-400 hover:text-red-600 p-1.5"><i class="fa-solid fa-xmark text-lg"></i></button>
                                </div>
                            </div>
                            <button type="button" onclick="tambahBundleItem()" class="mt-2 text-sm text-orange-600 hover:text-orange-700 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-plus-circle"></i> Tambah Item
                            </button>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white font-extrabold py-3 rounded-2xl hover:from-orange-600 hover:to-red-600 transition-all shadow-lg shadow-orange-200/50 active:scale-[0.98]">
                            <i class="fa-solid fa-gift mr-1"></i> Simpan Bundle
                        </button>
                    </form>
                </div>

                <!-- Daftar Bundle -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="text-base font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-boxes text-orange-500"></i> Daftar Bundle ({{ count($semuaBundle) }})
                    </h2>

                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
                    @endif

                    @if(count($semuaBundle) === 0)
                        <p class="text-gray-400 text-center py-8">Belum ada bundle. Buat bundle/paket menu sekarang!</p>
                    @else
                        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                            @foreach($semuaBundle as $bundle)
                            <div class="border border-gray-200 rounded-2xl p-4 hover:shadow-md transition-all {{ $bundle->aktif ? 'bg-white' : 'bg-gray-50 opacity-75' }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-extrabold text-gray-900 text-base">{{ $bundle->nama_bundle }}</h4>
                                            @if($bundle->aktif)
                                                <span class="bg-emerald-100 text-emerald-700 text-[9px] font-bold px-2 py-0.5 rounded-full">Aktif</span>
                                            @else
                                                <span class="bg-gray-200 text-gray-500 text-[9px] font-bold px-2 py-0.5 rounded-full">Nonaktif</span>
                                            @endif
                                        </div>
                                        @if($bundle->deskripsi)
                                        <p class="text-xs text-gray-500 mt-1">{{ $bundle->deskripsi }}</p>
                                        @endif

                                        <!-- Daftar item dalam bundle -->
                                        <div class="mt-2 space-y-1">
                                            @foreach($bundle->items as $item)
                                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                                <i class="fa-solid fa-caret-right text-gray-300"></i>
                                                <span>{{ $item->produk->nama_produk ?? 'N/A' }} <strong>x{{ $item->qty }}</strong></span>
                                                @if($item->produk)
                                                <span class="text-gray-400">@ Rp {{ number_format($item->produk->harga_normal, 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>

                                        <!-- Perhitungan harga -->
                                        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                                            <span class="text-gray-400 line-through text-xs">Normal: Rp {{ number_format($bundle->total_harga_normal, 0, ',', '.') }}</span>
                                            <span class="text-emerald-600 font-extrabold">Bundle: Rp {{ number_format($bundle->harga_bundle, 0, ',', '.') }}</span>
                                            @if($bundle->hemat > 0)
                                            <span class="bg-red-50 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full border border-red-200">
                                                Hemat Rp {{ number_format($bundle->hemat, 0, ',', '.') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                                    <form action="{{ route('owner.bundle.toggle', $bundle->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-xl border transition {{ $bundle->aktif ? 'bg-yellow-50 text-yellow-700 border-yellow-200 hover:bg-yellow-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}">
                                            <i class="fa-solid fa-{{ $bundle->aktif ? 'pause' : 'play' }} mr-1"></i> {{ $bundle->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('owner.bundle.hapus', $bundle->id) }}" method="POST" onsubmit="return confirm('Yakin hapus bundle {{ $bundle->nama_bundle }}?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-xl border text-red-600 border-red-200 bg-red-50 hover:bg-red-100 transition">
                                            <i class="fa-solid fa-trash-can mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab: Manajemen User -->
        <div id="tab-user" class="tab-content hidden">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-base font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-users-gear text-purple-500"></i> Manajemen User & Role
                </h2>
                <p class="text-xs text-gray-500 mb-4">Owner dapat mengubah role pengguna. Perubahan berlaku setelah login ulang.</p>
                <div class="table-scroll">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-xs uppercase">
                                <th class="p-3 border-b font-bold">ID</th>
                                <th class="p-3 border-b font-bold">Nama</th>
                                <th class="p-3 border-b font-bold">Username</th>
                                <th class="p-3 border-b font-bold">Email</th>
                                <th class="p-3 border-b font-bold">Role</th>
                                <th class="p-3 border-b font-bold text-center">Ubah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($semuaUser as $u)
                            <tr class="hover:bg-gray-50 text-gray-600">
                                <td class="p-3 font-mono font-bold text-xs">{{ $u->id_user }}</td>
                                <td class="p-3 font-semibold">{{ $u->nama }}</td>
                                <td class="p-3">{{ $u->username }}</td>
                                <td class="p-3 text-xs">{{ $u->email }}</td>
                                <td class="p-3">
                                    @if($u->role === 'Owner')
                                        <span class="bg-purple-50 text-purple-700 px-2.5 py-1 rounded-lg text-xs font-bold border border-purple-200">👑 Owner</span>
                                    @elseif($u->role === 'Kasir')
                                        <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg text-xs font-bold border border-blue-200">💼 Kasir</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded-lg text-xs font-bold border border-gray-200">👤 Customer</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    <form action="{{ route('owner.user.update-role', $u->id_user) }}" method="POST" class="flex items-center justify-center gap-1.5" onsubmit="return confirm('Ubah role {{ $u->nama }}?')">
                                        @csrf @method('PUT')
                                        <select name="role" class="border-2 border-gray-200 rounded-xl p-1.5 text-xs bg-white outline-none focus:border-purple-400">
                                            <option value="Customer" {{ $u->role == 'Customer' ? 'selected' : '' }}>Customer</option>
                                            <option value="Kasir" {{ $u->role == 'Kasir' ? 'selected' : '' }}>Kasir</option>
                                            <option value="Owner" {{ $u->role == 'Owner' ? 'selected' : '' }}>Owner</option>
                                        </select>
                                        <button type="submit" class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-sm hover:shadow-md">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <!-- ====== BOTTOM NAV (Mobile) ====== -->
    <nav class="bottom-nav">
        <a href="#" class="active" onclick="event.preventDefault(); window.scrollTo({top: 0, behavior: 'smooth'});">
            <i class="fa-solid fa-chart-simple"></i><span>Dashboard</span>
        </a>
        <a href="#" onclick="event.preventDefault(); switchTab('tab-produk');">
            <i class="fa-solid fa-plus-circle"></i><span>Tambah</span>
        </a>
        <a href="#" onclick="event.preventDefault(); switchTab('tab-inventaris');">
            <i class="fa-solid fa-boxes-stacked"></i><span>Stok</span>
        </a>
        <a href="#" onclick="event.preventDefault(); switchTab('tab-pesanan');">
            <i class="fa-solid fa-truck"></i><span>Pesanan</span>
        </a>
        <a href="{{ route('logout') }}">
            <i class="fa-solid fa-right-from-bracket"></i><span>Keluar</span>
        </a>
    </nav>

    @include('owner.detail_pesanan_modal')

    <!-- Struk Print -->
    <div id="area-cetak-struk-owner" class="hidden">
        <div id="struk-owner-content" style="text-align: center; margin-bottom: 10px;">
            <h3 style="margin: 0; font-size: 12pt; font-weight: bold;">WARUNG KOPI KITA</h3>
            <p style="margin: 2px 0; font-size: 8pt;">Jl. Anggrek No. 12, Bogor</p>
            <p style="margin: 2px 0; font-size: 8pt;">Telp: 0812-3456-7890</p>
            <p style="margin: 5px 0;">================================</p>
            <p style="margin: 2px 0; font-size: 8pt;" id="struk-owner-tanggal">Tgl: -</p>
            <p style="margin: 2px 0; font-size: 8pt;" id="struk-owner-id">Nota: -</p>
            <p style="margin: 2px 0; font-size: 8pt;" id="struk-owner-pembeli">Pembeli: -</p>
            <p style="margin: 5px 0; font-size: 8pt;">--------------------------------</p>
        </div>
        <div id="struk-owner-items" style="font-size: 9pt;"></div>
        <div style="font-size: 9pt; margin-top: 5px;">
            <p style="margin: 5px 0;">--------------------------------</p>
            <div style="display: flex; justify-content: space-between;">
                <span>Total Tagihan:</span>
                <span id="struk-owner-total">Rp 0</span>
            </div>
            <div id="struk-owner-bayar-row" style="display: flex; justify-content: space-between;">
                <span>Nominal Bayar:</span>
                <span id="struk-owner-bayar">Rp 0</span>
            </div>
            <div id="struk-owner-kembalian-row" style="display: flex; justify-content: space-between; font-weight: bold;">
                <span>Kembalian:</span>
                <span id="struk-owner-kembalian">Rp 0</span>
            </div>
            <p style="margin: 5px 0;">================================</p>
        </div>
        <div style="text-align: center; margin-top: 15px; font-size: 8pt;">
            <p style="margin: 2px 0; font-weight: bold;">TERIMA KASIH</p>
            <p style="margin: 2px 0;">Selamat Menikmati</p>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.getElementById(tabId).classList.remove('hidden');
            document.querySelectorAll('[id^="btn-tab-"]').forEach(btn => {
                btn.classList.remove('tab-active');
                btn.classList.add('tab-inactive');
            });
            const btn = document.getElementById('btn-' + tabId);
            if (btn) { btn.classList.remove('tab-inactive'); btn.classList.add('tab-active'); }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function formatRupiah(angka) {
            return 'Rp ' + parseFloat(angka).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        function cetakStrukOwner(idTransaksi) {
            fetch('/owner/transaksi/' + idTransaksi)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && res.data) isiDanCetakStrukOwner(res.data);
                else alert('Gagal: ' + (res.message || 'Unknown error'));
            })
            .catch(() => alert('Gagal memuat data.'));
        }

        function isiDanCetakStrukOwner(data) {
            let tanggal = data.created_at ? new Date(data.created_at).toLocaleString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' }) : '-';
            document.getElementById('struk-owner-id').innerText = 'Nota: ' + (data.id_transaksi || '-');
            document.getElementById('struk-owner-tanggal').innerText = 'Tgl: ' + tanggal;
            let infoPembeli = '';
            if (data.nama_pembeli) infoPembeli += 'Pembeli: ' + data.nama_pembeli;
            if (data.nomor_pesanan) infoPembeli += ' | No: ' + data.nomor_pesanan;
            document.getElementById('struk-owner-pembeli').innerText = infoPembeli || 'Pembeli: -';
            document.getElementById('struk-owner-total').innerText = formatRupiah(data.total_bayar || 0);
            
            if (data.pembayaran) {
                document.getElementById('struk-owner-bayar').innerText = formatRupiah(data.pembayaran.nominal_bayar || 0);
                document.getElementById('struk-owner-kembalian').innerText = formatRupiah(data.pembayaran.kembalian || 0);
                document.getElementById('struk-owner-bayar-row').style.display = 'flex';
                document.getElementById('struk-owner-kembalian-row').style.display = 'flex';
            } else {
                document.getElementById('struk-owner-bayar-row').style.display = 'none';
                document.getElementById('struk-owner-kembalian-row').style.display = 'none';
            }

            let htmlItems = '';
            if (data.detail_transaksis) {
                data.detail_transaksis.forEach(item => {
                    let subtotal = parseFloat(item.subtotal) || 0;
                    let qty = item.qty || 1;
                    let namaProduk = item.produk?.nama_produk || 'N/A';
                    htmlItems += `<div style="margin-bottom: 5px;">
                        <div style="font-weight: bold;">${namaProduk}</div>
                        <div style="display: flex; justify-content: space-between; font-size: 8pt;">
                            <span>${qty} x ${formatRupiah(item.subtotal / item.qty)}</span>
                            <span>${formatRupiah(subtotal)}</span>
                        </div>
                    </div>`;
                });
            }
            document.getElementById('struk-owner-items').innerHTML = htmlItems;

            let printArea = document.getElementById('area-cetak-struk-owner');
            printArea.classList.remove('hidden');
            setTimeout(() => { window.print(); printArea.classList.add('hidden'); }, 300);
        }

        function showEditBarang(id, nama, harga, stok, diskon) {
            document.getElementById('edit-nama').value = nama;
            document.getElementById('edit-harga').value = harga;
            document.getElementById('edit-stok').value = stok;
            document.getElementById('edit-diskon').value = diskon;
            document.getElementById('form-edit-barang').action = '/owner/produk/update-barang/' + id;
            document.getElementById('modal-edit-barang').classList.remove('hidden');
            document.getElementById('modal-edit-barang').classList.add('flex');
        }

        function hideEditBarang() {
            document.getElementById('modal-edit-barang').classList.add('hidden');
            document.getElementById('modal-edit-barang').classList.remove('flex');
        }

        document.addEventListener('click', function(e) {
            const modal = document.getElementById('modal-edit-barang');
            if (e.target === modal) hideEditBarang();
        });

        let bundleItemCounter = 1;
        function tambahBundleItem() {
            const container = document.getElementById('bundle-items-container');
            const div = document.createElement('div');
            div.className = 'bundle-item flex gap-2 items-center';
            div.innerHTML = `
                <select name="items[${bundleItemCounter}][id_produk]" required class="flex-1 border-2 border-gray-200 rounded-xl p-2 text-sm outline-none focus:border-orange-400 bg-white">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($produks as $prod)
                    <option value="{{ $prod->id_produk }}">{{ $prod->nama_produk }} (Rp {{ number_format($prod->harga_normal, 0, ',', '.') }})</option>
                    @endforeach
                </select>
                <input type="number" name="items[${bundleItemCounter}][qty]" value="1" min="1" class="w-16 border-2 border-gray-200 rounded-xl p-2 text-sm text-center outline-none focus:border-orange-400">
                <button type="button" onclick="hapusBundleItem(this)" class="text-red-400 hover:text-red-600 p-1.5"><i class="fa-solid fa-xmark text-lg"></i></button>
            `;
            container.appendChild(div);
            bundleItemCounter++;
        }
        function hapusBundleItem(btn) {
            const container = document.getElementById('bundle-items-container');
            if (container.children.length > 1) {
                btn.closest('.bundle-item').remove();
            } else {
                alert('Minimal 1 item dalam bundle!');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('chartOmsetHarian').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($grafikLabel) !!},
                    datasets: [{
                        label: 'Omset (Rp)',
                        data: {!! json_encode($grafikData) !!},
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { labels: { font: { family: 'Inter' } } } }
                }
            });

            // Responsive handler
            let isMobile = window.innerWidth < 1024;
            window.addEventListener('resize', function() {
                const now = window.innerWidth < 1024;
                if (isMobile !== now) {
                    isMobile = now;
                    if (!isMobile) { /* desktop mode */ }
                }
            });
        });
    </script>
</body>
</html>