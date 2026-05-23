<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Owner - Manajemen & Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .tab-active {
            background-color: #2563eb;
            color: white;
        }
        .tab-inactive {
            background-color: #e5e7eb;
            color: #374151;
        }
        .tab-inactive:hover {
            background-color: #d1d5db;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #area-cetak-struk-owner, #area-cetak-struk-owner * {
                visibility: visible;
            }
            #area-cetak-struk-owner {
                position: absolute;
                left: 0;
                top: 0;
                width: 50mm;
                font-family: 'Courier New', Courier, monospace;
                font-size: 10pt;
                line-height: 1.2;
                color: #000;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

    <nav class="bg-gray-800 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold"><i class="fa-solid fa-user-gear text-yellow-500 mr-2"></i> Panel Utama Owner</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm bg-gray-700 px-3 py-1 rounded">Log Sebagai: {{ session('nama', 'Pak Owner') }}</span>
                <a href="{{ route('logout') }}" class="text-sm bg-red-600 hover:bg-red-700 px-3 py-1 rounded transition"><i class="fa-solid fa-sign-out mr-1"></i> Keluar</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-6 space-y-6">
        
        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-linear-to-r from-blue-500 to-blue-600 p-6 rounded-xl shadow text-white flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-100 font-semibold uppercase">Total Omset Toko</p>
                    <h3 class="text-2xl font-black mt-1">Rp {{ number_format($totalOmsetSistem, 0, ',', '.') }}</h3>
                </div>
                <i class="fa-solid fa-wallet text-4xl text-blue-200/50"></i>
            </div>

            <div class="bg-linear-to-r from-green-500 to-green-600 p-6 rounded-xl shadow text-white flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-100 font-semibold uppercase">Jumlah Transaksi</p>
                    <h3 class="text-2xl font-black mt-1">{{ $totalTransaksiSistem }} Kali</h3>
                </div>
                <i class="fa-solid fa-receipt text-4xl text-green-200/50"></i>
            </div>

            <div class="bg-linear-to-r from-purple-500 to-purple-600 p-6 rounded-xl shadow text-white flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-100 font-semibold uppercase">Status Database</p>
                    <h3 class="text-xl font-bold mt-1">Sinkron & Aman</h3>
                </div>
                <i class="fa-solid fa-database text-4xl text-purple-200/50"></i>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2 border-b border-gray-300 pb-2">
            <button onclick="switchTab('tab-grafik')" id="btn-tab-grafik" class="tab-active px-5 py-2 rounded-t-lg text-sm font-bold transition cursor-pointer">
                <i class="fa-solid fa-chart-line mr-1"></i> Grafik Omset
            </button>
            <button onclick="switchTab('tab-produk')" id="btn-tab-produk" class="tab-inactive px-5 py-2 rounded-t-lg text-sm font-bold transition cursor-pointer">
                <i class="fa-solid fa-plus-circle mr-1"></i> Tambah Produk
            </button>
            <button onclick="switchTab('tab-inventaris')" id="btn-tab-inventaris" class="tab-inactive px-5 py-2 rounded-t-lg text-sm font-bold transition cursor-pointer">
                <i class="fa-solid fa-boxes-stacked mr-1"></i> Inventaris & Stok
            </button>
            <button onclick="switchTab('tab-transaksi')" id="btn-tab-transaksi" class="tab-inactive px-5 py-2 rounded-t-lg text-sm font-bold transition cursor-pointer">
                <i class="fa-solid fa-receipt mr-1"></i> Data Transaksi
            </button>
            <button onclick="switchTab('tab-user')" id="btn-tab-user" class="tab-inactive px-5 py-2 rounded-t-lg text-sm font-bold transition cursor-pointer">
                <i class="fa-solid fa-users-gear mr-1"></i> Manajemen User
            </button>
        </div>

        <!-- Tab Content: Grafik Omset -->
        <div id="tab-grafik" class="tab-content">
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-chart-line text-blue-500 mr-1"></i> Grafik Tren Omset Penjualan (7 Hari Terakhir)</h2>
                <div class="w-full h-64">
                    <canvas id="chartOmsetHarian"></canvas>
                </div>
            </div>
        </div>

        <!-- Tab Content: Tambah Produk -->
        <div id="tab-produk" class="tab-content hidden">
            <div class="bg-white p-6 rounded-xl shadow max-w-lg mx-auto">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-plus-circle text-blue-500 mr-1"></i> Tambah Produk Baru</h2>
                
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 text-sm">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">{{ session('error') }}</div>
                @endif

                <form action="{{ route('owner.produk.tambah') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @csrf                    
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Kode / ID Produk</label>
                        <input type="text" name="id_produk" required class="w-full border rounded p-2 text-sm focus:outline-blue-500" placeholder="Contoh: P004">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Nama Produk</label>
                        <input type="text" name="nama_produk" required class="w-full border rounded p-2 text-sm focus:outline-blue-500" placeholder="Nama makanan / minuman">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Harga Jual</label>
                            <input type="number" name="harga_normal" required class="w-full border rounded p-2 text-sm focus:outline-blue-500" placeholder="Rupiah">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Jumlah Stok</label>
                            <input type="number" name="stok" required class="w-full border rounded p-2 text-sm focus:outline-blue-500" placeholder="Pcs">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Pasang Diskon (Opsional)</label>
                        <select name="id_diskon" class="w-full border rounded p-2 text-sm focus:outline-blue-500 bg-white">
                            <option value="">-- Tidak Ada Diskon --</option>
                            @foreach($diskons as $diskon)
                            <option value="{{ $diskon->id_diskon }}">{{ $diskon->nama_diskon }} ({{ $diskon->nilai }}%)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Foto Produk</label>
                        <input type="file" name="foto" required class="w-full border rounded p-2 text-sm" accept="image/*">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded transition shadow">
                        <i class="fa-solid fa-save mr-1"></i> Simpan Data ke Toko
                    </button>
                </form>
            </div>
        </div>

        <!-- Tab Content: Inventaris & Stok Barang -->
        <div id="tab-inventaris" class="tab-content hidden">
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-boxes-stacked text-yellow-500 mr-1"></i> Data Inventaris & Stok Barang</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 text-sm uppercase">
                                <th class="p-3 border-b">ID</th>
                                <th class="p-3 border-b">Nama Produk</th>
                                <th class="p-3 border-b">Harga Normal</th>
                                <th class="p-3 border-b">Stok</th>
                                <th class="p-3 border-b">Diskon Aktif</th>
                                <th class="p-3 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y">
                            @foreach($produks as $p)
                            <tr class="hover:bg-gray-50 text-gray-600">
                                <td class="p-3 font-mono font-bold">{{ $p->id_produk }}</td>
                                <td class="p-3 font-semibold text-gray-800">{{ $p->nama_produk }}</td>
                                <td class="p-3">Rp {{ number_format($p->harga_normal, 0, ',', '.') }}</td>
                                <td class="p-3">
                                    @if($p->stok <= 10)
                                        <span class="text-red-600 font-bold bg-red-100 px-2 py-0.5 rounded"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Kritis: {{ $p->stok }}</span>
                                    @else
                                        <span class="text-green-700 font-medium">{{ $p->stok }} Pcs</span>
                                    @endif
                                </td>
                                <td class="p-3 text-xs">
                                    {{ $p->diskon ? $p->diskon->nama_diskon : 'Kosong' }}
                                </td>
                                <td class="p-3 text-center">
                                    <form action="{{ route('owner.produk.hapus', $p->id_produk) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini dari toko?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 px-2 py-1 rounded border border-red-200 bg-red-50 hover:bg-red-100 transition">
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

        <!-- Tab Content: Data Transaksi -->
        <div id="tab-transaksi" class="tab-content hidden">
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-receipt text-green-500 mr-1"></i> Data Transaksi & Bukti Struk</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 text-sm uppercase">
                                <th class="p-3 border-b">ID Transaksi</th>
                                <th class="p-3 border-b">Tanggal</th>
                                <th class="p-3 border-b">Pembeli</th>
                                <th class="p-3 border-b">No. Pesanan</th>
                                <th class="p-3 border-b">Item</th>
                                <th class="p-3 border-b">Total</th>
                                <th class="p-3 border-b">Metode</th>
                                <th class="p-3 border-b text-center">Struk</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y">
                            @forelse($semuaTransaksi as $trx)
                            <tr class="hover:bg-gray-50 text-gray-600">
                                <td class="p-3 font-mono font-bold text-xs">{{ $trx->id_transaksi }}</td>
                                <td class="p-3 text-xs">{{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i') : '-' }}</td>
                                <td class="p-3">{{ $trx->nama_pembeli ?? '-' }}</td>
                                <td class="p-3">{{ $trx->nomor_pesanan ?? '-' }}</td>
                                <td class="p-3 text-xs">
                                    @foreach($trx->detailTransaksis as $dt)
                                        {{ $dt->produk->nama_produk ?? 'N/A' }} x{{ $dt->qty }}<br>
                                    @endforeach
                                </td>
                                <td class="p-3 font-bold">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                                <td class="p-3 text-xs">
                                    @if($trx->pembayaran)
                                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{ $trx->pembayaran->metode_bayar }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    <button onclick="cetakStrukOwner('{{ $trx->id_transaksi }}')" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2.5 py-1 rounded text-xs font-bold transition cursor-pointer">
                                        <i class="fa-solid fa-print mr-1"></i> Cetak
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-gray-400">Belum ada transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab Content: Manajemen User -->
        <div id="tab-user" class="tab-content hidden">
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-users-gear text-purple-500 mr-1"></i> Manajemen User & Role</h2>
                <p class="text-xs text-gray-500 mb-3">Owner / Head Admin dapat mengubah role pengguna. Customer yang ingin menjadi Kasir harus diubah role-nya oleh Admin.</p>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 text-sm uppercase">
                                <th class="p-3 border-b">ID</th>
                                <th class="p-3 border-b">Nama</th>
                                <th class="p-3 border-b">Username</th>
                                <th class="p-3 border-b">Email</th>
                                <th class="p-3 border-b">Role Saat Ini</th>
                                <th class="p-3 border-b text-center">Ubah Role</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y">
                            @foreach($semuaUser as $u)
                            <tr class="hover:bg-gray-50 text-gray-600">
                                <td class="p-3 font-mono font-bold">{{ $u->id_user }}</td>
                                <td class="p-3 font-semibold">{{ $u->nama }}</td>
                                <td class="p-3">{{ $u->username }}</td>
                                <td class="p-3 text-xs">{{ $u->email }}</td>
                                <td class="p-3">
                                    @if($u->role === 'Owner')
                                        <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs font-bold">Owner</span>
                                    @elseif($u->role === 'Kasir')
                                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">Kasir</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-bold">Customer</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    <form action="{{ route('owner.user.update-role', $u->id_user) }}" method="POST" class="flex items-center justify-center gap-2" onsubmit="return confirm('Ubah role {{ $u->nama }}?')">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="border rounded p-1 text-xs bg-white">
                                            <option value="Customer" {{ $u->role == 'Customer' ? 'selected' : '' }}>Customer</option>
                                            <option value="Kasir" {{ $u->role == 'Kasir' ? 'selected' : '' }}>Kasir</option>
                                            <option value="Owner" {{ $u->role == 'Owner' ? 'selected' : '' }}>Owner</option>
                                        </select>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1 rounded text-xs font-bold transition cursor-pointer">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-[10px] text-gray-400 mt-3"><i class="fa-solid fa-info-circle mr-1"></i> Perubahan role berlaku setelah pengguna login ulang.</p>
            </div>
        </div>

    </main>

    <!-- Area Cetak Struk Owner (hidden) -->
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
            <p style="margin: 2px 0;">Selamat Menikmati Kembali</p>
        </div>
    </div>

    <script>
        // Switch Tab
        function switchTab(tabId) {
            // Sembunyikan semua konten tab
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Tampilkan tab yang dipilih
            document.getElementById(tabId).classList.remove('hidden');
            
            // Update style button
            document.querySelectorAll('[id^="btn-tab-"]').forEach(btn => {
                btn.classList.remove('tab-active');
                btn.classList.add('tab-inactive');
            });
            document.getElementById('btn-' + tabId).classList.remove('tab-inactive');
            document.getElementById('btn-' + tabId).classList.add('tab-active');
        }

        // Format Rupiah
        function formatRupiah(angka) {
            return 'Rp ' + parseFloat(angka).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        // Cetak Struk dari Data Transaksi (via API)
        function cetakStrukOwner(idTransaksi) {
            fetch('/owner/transaksi/' + idTransaksi)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && res.data) {
                    isiDanCetakStrukOwner(res.data);
                } else {
                    alert('Gagal memuat data struk: ' + (res.message || 'Unknown error'));
                }
            })
            .catch(err => {
                alert('Gagal memuat data struk. Silakan coba lagi.');
                console.error(err);
            });
        }

        function isiDanCetakStrukOwner(data) {
            let tanggal = data.created_at ? new Date(data.created_at).toLocaleString('id-ID', {
                year: 'numeric', month: 'long', day: 'numeric',
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            }) : '-';

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
                    let hargaSatuan = item.subtotal / item.qty;
                    htmlItems += `
                    <div style="margin-bottom: 5px;">
                        <div style="font-weight: bold;">${namaProduk}</div>
                        <div style="display: flex; justify-content: space-between; font-size: 8pt;">
                            <span>${qty} x ${formatRupiah(hargaSatuan)}</span>
                            <span>${formatRupiah(subtotal)}</span>
                        </div>
                    </div>`;
                });
            }
            document.getElementById('struk-owner-items').innerHTML = htmlItems;

            // Cetak
            let printArea = document.getElementById('area-cetak-struk-owner');
            printArea.classList.remove('hidden');
            setTimeout(() => {
                window.print();
                printArea.classList.add('hidden');
            }, 300);
        }

        // Inisialisasi Chart
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('chartOmsetHarian').getContext('2d');
            const labelsHari = {!! json_encode($grafikLabel) !!};
            const dataOmset = {!! json_encode($grafikData) !!};

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsHari,
                    datasets: [{
                        label: 'Omset Pendapatan (Rp)',
                        data: dataOmset,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
</body>
</html>