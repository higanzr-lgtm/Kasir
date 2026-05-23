<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Kasir POS - Laravel</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Gaya khusus saat mencetak (Printer Thermal 58mm) */
        @media print {
            body * {
                visibility: hidden; /* Sembunyikan semua elemen halaman utama */
            }
            #area-cetak-struk, #area-cetak-struk * {
                visibility: visible; /* Hanya tampilkan area struk */
            }
            #area-cetak-struk {
                position: absolute;
                left: 0;
                top: 0;
                width: 50mm; /* Lebar area cetak printer thermal kecil */
                font-family: 'Courier New', Courier, monospace; /* Font khas struk belanja */
                font-size: 10pt;
                line-height: 1.2;
                color: #000;
            }
            /* Sembunyikan modal dan navbar saat proses cetak */
            #payment-modal, #success-modal, nav {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold"><i class="fa-solid fa-cash-register mr-2"></i> Sistem Kasir POS</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm bg-blue-700 px-3 py-1 rounded">Kasir: {{ session('nama', 'Siti Kasir') }} (ID: {{ session('id_user', 2) }})</span>
                <a href="{{ route('logout') }}" class="text-sm bg-red-500 hover:bg-red-600 px-3 py-1 rounded transition"><i class="fa-solid fa-sign-out mr-1"></i> Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">
                <i class="fa-solid fa-boxes-stacked text-blue-500 mr-2"></i> Daftar Produk
            </h2>
    
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($produks as $produk)
                <div class="border rounded-lg p-4 hover:shadow-md transition bg-gray-50 flex flex-col justify-between">
                    <div>
                        <div class="w-full h-32 mb-3 overflow-hidden rounded-md bg-gray-200">
                            <img src="{{ asset('images/menu/' . $produk->foto) }}" 
                                alt="{{ $produk->nama_produk }}" 
                                class="w-full h-full object-cover">
                        </div>

                        <h3 class="font-bold text-gray-700">{{ $produk->nama_produk }}</h3>
                        <p class="text-xs text-gray-500">Stok: <span class="font-semibold">{{ $produk->stok }}</span></p>
                    </div>
                    
                    <div class="mt-4">
                        @if($produk->diskon)
                            <p class="text-xs text-red-500 line-through">Rp {{ number_format($produk->harga_normal, 0, ',', '.') }}</p>
                            <p class="text-lg font-bold text-green-600">Rp {{ number_format($produk->getHargaNet(), 0, ',', '.') }}</p>
                            <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded font-bold">{{ $produk->diskon->nama_diskon }}</span>
                        @else
                            <p class="text-lg font-bold text-gray-800">Rp {{ number_format($produk->harga_normal, 0, ',', '.') }}</p>
                        @endif
                        
                        <button onclick="tambahKeKeranjang('{{ $produk->id_produk }}', '{{ $produk->nama_produk }}', {{ $produk->getHargaNet() }})" class="mt-2 w-full bg-blue-500 text-white text-sm py-1.5 rounded hover:bg-blue-600 transition">
                            <i class="fa-solid fa-plus mr-1"></i> Tambah
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow flex flex-col justify-between h-fit">
            <div>
                <h2 class="text-lg font-semibold mb-4 text-gray-800"><i class="fa-solid fa-shopping-cart text-blue-500 mr-2"></i> Keranjang Belanja</h2>
                
                <div id="keranjang-list" class="space-y-3 divide-y divide-gray-100 max-h-60 overflow-y-auto pr-1">
                    <p class="text-gray-400 text-center text-sm py-8">Keranjang masih kosong</p>
                </div>
            </div>

            <form id="form-data-pembeli" class="border-t pt-4 mt-4 space-y-3" onsubmit="return false;">
                <h3 class="text-sm font-semibold text-gray-700"><i class="fa-solid fa-user mr-1"></i> Data Pembeli</h3>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-0.5">Nama Pembeli</label>
                    <input type="text" id="nama_pembeli" class="w-full border rounded p-1.5 text-sm" placeholder="Masukkan nama...">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-0.5">No. Pesanan</label>
                    <input type="text" id="nomor_pesanan" class="w-full border rounded p-1.5 text-sm" value="{{ $nomorPesananOtomatis }}">
                </div>
            </form>

            <div class="border-t pt-4 mt-4">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-600 font-semibold">Total Tagihan:</span>
                    <span id="grand-total" class="text-2xl font-black text-blue-600">Rp 0</span>
                </div>

                <div class="space-y-2">
                    <button onclick="prosesCheckout()" id="btn-checkout" class="w-full bg-green-500 text-white font-bold py-2.5 rounded shadow hover:bg-green-600 transition disabled:bg-gray-300" disabled>
                        <i class="fa-solid fa-file-invoice-dollar mr-2"></i> Simpan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </main>

    <div id="payment-modal" class="hidden fixed inset-0 bg-black/50 items-center justify-center p-4">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full shadow-xl">
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4"><i class="fa-solid fa-money-bill-wave text-green-500 mr-2"></i> Menu Pembayaran</h3>
            <p class="text-sm text-gray-600">ID Transaksi: <span id="modal-trx-id" class="font-mono font-bold"></span></p>
            <p class="text-sm text-gray-600 mb-4">Total yang harus dibayar: <span id="modal-trx-total" class="font-bold text-blue-600"></span></p>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Metode Pembayaran</label>
                    <select id="metode_bayar" class="w-full border rounded p-2 text-sm" onchange="toggleNominalBayar()">
                        <option value="Tunai">Uang Tunai</option>
                        <option value="QRIS">Digital QRIS</option>
                    </select>
                </div>
                <div id="div_nominal_bayar">
                    <label class="block text-xs font-bold text-gray-600 mb-1">Nominal Uang Diterima</label>
                    <input type="number" id="nominal_bayar" class="w-full border rounded p-2 text-sm" placeholder="Masukkan jumlah uang...">
                </div>
                <button onclick="bayarSekarang()" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700 transition">
                    Konfirmasi Lunas
                </button>
            </div>
        </div>
    </div>

    <div id="success-modal" class="hidden fixed inset-0 bg-black/60 items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <i class="fa-solid fa-circle-check text-green-600 text-4xl"></i>
            </div>
            
            <h3 class="text-xl font-black text-gray-900 mb-1">Pembayaran Berhasil!</h3>
            <p class="text-sm text-gray-500 mb-6">Transaksi telah dicatat ke dalam sistem.</p>
            
            <div class="bg-gray-50 rounded-xl p-4 mb-6 space-y-2 text-left border border-gray-100">
                <div id="pop-sukses-pembeli-row" class="flex justify-between text-xs text-gray-500" style="display: none;">
                    <span>Pembeli:</span>
                    <span id="pop-sukses-pembeli" class="font-bold text-gray-800">-</span>
                </div>
                <div class="flex justify-between text-xs text-gray-500">
                    <span>Tanggal:</span>
                    <span id="pop-sukses-tanggal" class="font-bold text-gray-800">-</span>
                </div>
                <div class="flex justify-between text-xs text-gray-500">
                    <span>Metode Bayar:</span>
                    <span id="pop-sukses-metode" class="font-bold text-gray-800">-</span>
                </div>
                <div class="flex justify-between text-xs text-gray-500">
                    <span>Total Tagihan:</span>
                    <span id="pop-sukses-total" class="font-bold text-gray-800">-</span>
                </div>
                <div id="pop-sukses-kembalian-row" class="flex justify-between text-sm pt-2 border-t border-dashed border-gray-200">
                    <span class="font-semibold text-gray-700">Uang Kembalian:</span>
                    <span id="pop-sukses-kembalian" class="font-black text-green-600 text-lg">-</span>
                </div>
            </div>

            <button onclick="eksekusiCetakStruk()" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Struk & Selesai
            </button>
        </div>
    </div>

    <div id="qrisModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white p-6 rounded-2xl shadow-xl w-96 text-center">
        <h2 class="text-xl font-bold mb-2 text-gray-800">Scan QRIS Pembayaran</h2>
        <p class="text-sm text-gray-600 mb-4">Total: <span id="qris-modal-total" class="font-bold text-blue-600"></span></p>
        
        <div class="flex justify-center mb-4 relative">
            <div id="qris-loader" class="absolute inset-0 flex items-center justify-center bg-white/80 rounded-lg hidden">
                <i class="fa-solid fa-spinner fa-spin text-3xl text-blue-500"></i>
            </div>
            <img id="qris-image" src="" alt="QRIS Code" class="w-64 h-64 border rounded-lg shadow-sm">
        </div>
        
        <button onclick="prosesPembayaranQRIS()" id="btn-proses-qris" class="w-full bg-green-500 text-white py-2 mb-2 rounded-lg font-bold hover:bg-green-600 flex justify-center items-center gap-2">
            <i class="fa-solid fa-check-circle"></i> Cek Status Pembayaran
        </button>
        <button onclick="tutupQris()" class="w-full bg-red-500 text-white py-2 rounded-lg font-bold hover:bg-red-600">Batal</button>
    </div>
</div>

    <div id="area-cetak-struk" class="hidden">
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
            <p style="margin: 2px 0;" id="struk-kasir">Kasir: {{ session('nama', 'Siti Kasir') }}</p>
            <p style="margin: 5px 0;">--------------------------------</p>
        </div>

        <div id="struk-items" style="font-size: 9pt;"></div>

        <div style="font-size: 9pt; margin-top: 5px;">
            <p style="margin: 5px 0;">--------------------------------</p>
            <div style="display: flex; justify-content: space-between;">
                <span>Total Tagihan:</span>
                <span id="struk-total">Rp 0</span>
            </div>
            <div id="struk-bayar-row" style="display: flex; justify-content: space-between;">
                <span>Nominal Bayar:</span>
                <span id="struk-bayar">Rp 0</span>
            </div>
            <div id="struk-kembalian-row" style="display: flex; justify-content: space-between; font-weight: bold;">
                <span>Kembalian:</span>
                <span id="struk-kembalian">Rp 0</span>
            </div>
            <p style="margin: 5px 0;">================================</p>
        </div>

        <div style="text-align: center; margin-top: 15px; font-size: 8pt;">
            <p style="margin: 2px 0; font-weight: bold;">TERIMA KASIH</p>
            <p style="margin: 2px 0;">Selamat Menikmati Kembali</p>
        </div>
    </div>

    <script>
        let keranjang = [];

        // Helper Fungsi: Mengubah angka biasa menjadi format Rupiah asli (Rp xx.xxx)
        function formatRupiah(angka) {
            return 'Rp ' + parseFloat(angka).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        function tambahKeKeranjang(id, nama, harga) {
            let index = keranjang.findIndex(item => item.id_produk === id);
            if (index !== -1) {
                keranjang[index].qty++;
            } else {
                keranjang.push({ id_produk: id, nama_produk: nama, harga: harga, qty: 1, diskon_persen: 0 });
            }
            updateTampilanKeranjang();
        }
        

        function updateTampilanKeranjang() {
            let listElement = document.getElementById('keranjang-list');
            let totalElement = document.getElementById('grand-total');
            let btnCheckout = document.getElementById('btn-checkout');
            
            if (keranjang.length === 0) {
                listElement.innerHTML = '<p class="text-gray-400 text-center text-sm py-8">Keranjang masih kosong</p>';
                totalElement.innerText = 'Rp 0';
                btnCheckout.disabled = true;
                return;
            }

            btnCheckout.disabled = false;
            let html = '';
            let grandTotal = 0;

            keranjang.forEach((item, i) => {
                let subtotal = item.harga * item.qty;
                let diskonItem = item.diskon_persen || 0;
                let potongan = subtotal * (diskonItem / 100);
                let subFinal = subtotal - potongan;
                grandTotal += subFinal;

                html += `
                <div class="flex justify-between items-start py-2 text-sm">
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <h4 class="font-bold text-gray-700">${item.nama_produk}</h4>
                            <span class="font-bold text-gray-600">${formatRupiah(subFinal)}</span>
                        </div>
                        <p class="text-xs text-gray-400">${item.qty} x ${formatRupiah(item.harga)}</p>
                        <div class="flex items-center gap-1 mt-1">
                            <input type="number" min="0" max="100" step="0.1" value="${diskonItem}"
                                class="w-14 border border-yellow-300 rounded p-0.5 text-xs text-center text-yellow-700 bg-yellow-50 focus:outline-yellow-500"
                                onchange="ubahDiskonItem(${i}, this.value)" oninput="ubahDiskonItem(${i}, this.value)">
                            <span class="text-[10px] text-yellow-600 font-bold">%</span>
                            ${potongan > 0 ? `<span class="text-[9px] text-red-400">(-${formatRupiah(potongan)})</span>` : ''}
                        </div>
                    </div>
                </div>`;
            });

            listElement.innerHTML = html;
            totalElement.innerText = formatRupiah(grandTotal);
        }

        function ubahDiskonItem(index, value) {
            let diskon = parseFloat(value) || 0;
            if (diskon < 0) diskon = 0;
            if (diskon > 100) diskon = 100;
            keranjang[index].diskon_persen = diskon;
            updateTampilanKeranjang();
        }

        let currentTrxId = '';
        let currentTotal = 0;

        function toggleNominalBayar() {
            let metode = document.getElementById('metode_bayar').value;
            let divNominal = document.getElementById('div_nominal_bayar');
            if (metode === 'QRIS') {
                divNominal.classList.add('hidden');
            } else {
                divNominal.classList.remove('hidden');
            }
        }

        function prosesCheckout() {
            let namaPembeli = document.getElementById('nama_pembeli').value.trim();
            let nomorPesanan = document.getElementById('nomor_pesanan').value.trim();

            fetch("{{ route('transaksi.simpan') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    id_user: "{{ session('id_user', 2) }}",
                    items: keranjang,
                    nama_pembeli: namaPembeli || null,
                    nomor_pesanan: nomorPesanan || null
                })
            })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    currentTrxId = res.id_transaksi;
                    currentTotal = res.total_tagihan;
                    
                    document.getElementById('modal-trx-id').innerText = currentTrxId;
                    document.getElementById('modal-trx-total').innerText = formatRupiah(currentTotal);
                    
                    // Reset dropdown dan nominal input
                    document.getElementById('metode_bayar').value = 'Tunai';
                    document.getElementById('nominal_bayar').value = '';
                    toggleNominalBayar();

                    let modal = document.getElementById('payment-modal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } else {
                    alert(res.message);
                }
            });
        }

        function bayarSekarang() {
            let nominal = document.getElementById('nominal_bayar').value;
            let metode = document.getElementById('metode_bayar').value;

            if (metode === 'QRIS') {
                // Tampilkan Modal QRIS
                document.getElementById('qrisModal').classList.remove('hidden');
                document.getElementById('qrisModal').classList.add('flex');
                
                // Generate QRIS dengan sistem API pihak ketiga secara dinamis sesuai total
                document.getElementById('qris-modal-total').innerText = formatRupiah(currentTotal);
                let qrData = `QRIS-TRX-${currentTrxId}-TOTAL-${currentTotal}`;
                document.getElementById('qris-image').src = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrData)}`;
                
                return; // Hentikan proses agar tidak langsung lanjut ke fetch
            }

            if(!nominal || nominal < currentTotal) {
                alert("Nominal uang yang diterima kurang atau belum diisi!");
                return;
            }

            fetch("{{ route('pembayaran.proses') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    id_transaksi: currentTrxId,
                    metode_bayar: metode,
                    nominal_bayar: nominal
                })
            })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    let dataTrx = res.data;

                    // Gunakan tanggal dari server (created_at)
                    let tanggalRealtime = new Date(dataTrx.created_at).toLocaleString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });

                    // 1. ISI DATA NOTA STRUK TERSEMBUNYI
                    document.getElementById('struk-id').innerText = "Nota: " + dataTrx.id_transaksi;
                    document.getElementById('struk-tanggal').innerText = "Tgl : " + tanggalRealtime;
                    document.getElementById('struk-total').innerText = formatRupiah(dataTrx.total_bayar);
                    document.getElementById('struk-bayar').innerText = formatRupiah(dataTrx.pembayaran.nominal_bayar);
                    document.getElementById('struk-kembalian').innerText = formatRupiah(dataTrx.pembayaran.kembalian);

                    // Tampilkan data pembeli di struk (jika ada)
                    let strukPembeli = document.getElementById('struk-pembeli');
                    if (dataTrx.nama_pembeli || dataTrx.nomor_pesanan) {
                        let infoPembeli = '';
                        if (dataTrx.nama_pembeli) infoPembeli += 'Pembeli: ' + dataTrx.nama_pembeli;
                        if (dataTrx.nomor_pesanan) infoPembeli += (infoPembeli ? ' | ' : '') + 'No: ' + dataTrx.nomor_pesanan;
                        document.getElementById('struk-pembeli').innerText = infoPembeli;
                        document.getElementById('struk-pembeli').style.display = 'block';
                    } else {
                        document.getElementById('struk-pembeli').style.display = 'none';
                    }

                    let htmlItems = '';
                    dataTrx.detail_transaksis.forEach(item => {
                        let subtotal = parseFloat(item.subtotal);
                        htmlItems += `
                        <div style="margin-bottom: 5px;">
                            <div style="font-weight: bold;">${item.produk.nama_produk}</div>
                            <div style="display: flex; justify-content: space-between; font-size: 8pt;">
                                <span>${item.qty} x ${formatRupiah(item.subtotal / item.qty)}</span>
                                <span>${formatRupiah(subtotal)}</span>
                            </div>
                        </div>`;
                    });
                    document.getElementById('struk-items').innerHTML = htmlItems;

                    document.getElementById('struk-bayar-row').style.display = 'flex';
                    document.getElementById('struk-kembalian-row').style.display = 'flex';

                    // Tampilkan data pembeli di modal sukses (jika ada)
                    let popPembeliRow = document.getElementById('pop-sukses-pembeli-row');
                    let popPembeliInfo = '';
                    if (dataTrx.nama_pembeli) popPembeliInfo += dataTrx.nama_pembeli;
                    if (dataTrx.nomor_pesanan) popPembeliInfo += (popPembeliInfo ? ' - ' : '') + 'No. ' + dataTrx.nomor_pesanan;
                    if (popPembeliInfo) {
                        document.getElementById('pop-sukses-pembeli').innerText = popPembeliInfo;
                        popPembeliRow.style.display = 'flex';
                    } else {
                        popPembeliRow.style.display = 'none';
                    }

                    // 2. TAMPILKAN POP-UP MODAL SUKSES DENGAN FORMAT RUPIAH & KEMBALIAN
                    document.getElementById('pop-sukses-tanggal').innerText = tanggalRealtime;
                    document.getElementById('pop-sukses-metode').innerText = dataTrx.pembayaran.metode_bayar;
                    document.getElementById('pop-sukses-total').innerText = formatRupiah(dataTrx.total_bayar);
                    document.getElementById('pop-sukses-kembalian').innerText = formatRupiah(dataTrx.pembayaran.kembalian);
                    document.getElementById('pop-sukses-kembalian-row').style.display = 'flex';

                    // Sembunyikan modal input, munculkan modal sukses
                    document.getElementById('payment-modal').classList.remove('flex');
                    document.getElementById('payment-modal').classList.add('hidden');
                    
                    let successModal = document.getElementById('success-modal');
                    successModal.classList.remove('hidden');
                    successModal.classList.add('flex');

                    // Mainkan Animasi Confetti
                    confetti({
                        particleCount: 150,
                        spread: 80,
                        origin: { y: 0.6 }
                    });

                } else {
                    alert(res.message);
                }
            });
        }

        function eksekusiCetakStruk() {
            document.getElementById('success-modal').classList.remove('flex');
            document.getElementById('success-modal').classList.add('hidden');
            
            setTimeout(() => {
                window.print();
                location.reload(); 
            }, 300);
        }

        function tutupQris() {
            document.getElementById('qrisModal').classList.add('hidden');
            document.getElementById('qrisModal').classList.remove('flex');
        }

        function prosesPembayaranQRIS() {
            // Animasi loading saat cek pembayaran
            let btnProses = document.getElementById('btn-proses-qris');
            let qrisLoader = document.getElementById('qris-loader');
            
            btnProses.disabled = true;
            btnProses.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
            qrisLoader.classList.remove('hidden');

            // Simulasi delay cek status sistem (1.5 detik)
            setTimeout(() => {
                // Kirim data ke controller (sama seperti alur pembayaran tunai)
                fetch("{{ route('pembayaran.proses') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id_transaksi: currentTrxId,
                        metode_bayar: 'QRIS',
                        nominal_bayar: currentTotal // Untuk QRIS, nominal dianggap pas
                    })
                })
                .then(res => res.json())
                .then(res => {
                    // Kembalikan state tombol
                    btnProses.disabled = false;
                    btnProses.innerHTML = '<i class="fa-solid fa-check-circle"></i> Cek Status Pembayaran';
                    qrisLoader.classList.add('hidden');
                    
                    if(res.status === 'success') {
                        // Tutup modal QRIS
                        tutupQris();
                        
                        let dataTrx = res.data;
                        
                        // Gunakan tanggal dari server (created_at)
                        let tanggalRealtime = new Date(dataTrx.created_at).toLocaleString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });

                        // 1. ISI DATA NOTA STRUK TERSEMBUNYI
                        document.getElementById('struk-id').innerText = "Nota: " + dataTrx.id_transaksi;
                        document.getElementById('struk-tanggal').innerText = "Tgl : " + tanggalRealtime;
                        document.getElementById('struk-total').innerText = formatRupiah(dataTrx.total_bayar);
                        document.getElementById('struk-bayar').innerText = formatRupiah(dataTrx.pembayaran.nominal_bayar);
                        document.getElementById('struk-kembalian').innerText = formatRupiah(dataTrx.pembayaran.kembalian);
                        document.getElementById('struk-bayar-row').style.display = 'none';
                        document.getElementById('struk-kembalian-row').style.display = 'none';

                        let htmlItems = '';
                        dataTrx.detail_transaksis.forEach(item => {
                            let subtotal = parseFloat(item.subtotal);
                            htmlItems += `
                            <div style="margin-bottom: 5px;">
                                <div style="font-weight: bold;">${item.produk.nama_produk}</div>
                                <div style="display: flex; justify-content: space-between; font-size: 8pt;">
                                    <span>${item.qty} x ${formatRupiah(item.subtotal / item.qty)}</span>
                                    <span>${formatRupiah(subtotal)}</span>
                                </div>
                            </div>`;
                        });
                        document.getElementById('struk-items').innerHTML = htmlItems;
                        
                        // Update tampilan modal sukses
                        document.getElementById('pop-sukses-tanggal').innerText = tanggalRealtime;
                        document.getElementById('pop-sukses-metode').innerText = 'QRIS';
                        document.getElementById('pop-sukses-total').innerText = formatRupiah(dataTrx.total_bayar);
                        document.getElementById('pop-sukses-kembalian-row').style.display = 'none';

                        document.getElementById('payment-modal').classList.remove('flex');
                        document.getElementById('payment-modal').classList.add('hidden');

                        document.getElementById('success-modal').classList.remove('hidden');
                        document.getElementById('success-modal').classList.add('flex');
                        
                        // Mainkan Animasi Confetti
                        confetti({
                            particleCount: 200,
                            spread: 100,
                            origin: { y: 0.5 },
                            colors: ['#26ccff', '#a25afd', '#ff5e7e', '#88ff5a', '#fcff42', '#ffa62d', '#ff36ff']
                        });
                    } else {
                        alert(res.message);
                    }
                });
            }, 1500);
        }
    </script>
</body>
</html>