<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian Online - Warung Kopi Kita</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 font-sans min-h-screen">

    <nav class="bg-emerald-700 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold"><i class="fa-solid fa-store mr-2"></i> Warung Kopi Kita</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm bg-emerald-800 px-3 py-1 rounded">
                    <i class="fa-solid fa-user mr-1"></i> {{ session('nama', 'Customer') }}
                </span>
                <a href="{{ route('logout') }}" class="text-sm bg-red-500 hover:bg-red-600 px-3 py-1 rounded transition">
                    <i class="fa-solid fa-sign-out mr-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Daftar Produk -->
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">
                <i class="fa-solid fa-mug-hot text-emerald-500 mr-2"></i> Menu Kami
            </h2>

            <div id="menu-produk" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($produks as $produk)
                <div class="border rounded-lg p-4 hover:shadow-md transition bg-gray-50 flex flex-col justify-between">
                    <div>
                        <div class="w-full h-32 mb-3 overflow-hidden rounded-md bg-gray-200">
                            <img src="{{ asset('images/menu/' . $produk->foto) }}" 
                                alt="{{ $produk->nama_produk }}" 
                                class="w-full h-full object-cover">
                        </div>
                        <h3 class="font-bold text-gray-700">{{ $produk->nama_produk }}</h3>
                        @if($produk->diskon)
                            <p class="text-xs text-red-500 line-through">Rp {{ number_format($produk->harga_normal, 0, ',', '.') }}</p>
                            <p class="text-lg font-bold text-green-600">Rp {{ number_format($produk->getHargaNet(), 0, ',', '.') }}</p>
                            <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded font-bold">{{ $produk->diskon->nama_diskon }}</span>
                        @else
                            <p class="text-lg font-bold text-gray-800">Rp {{ number_format($produk->harga_normal, 0, ',', '.') }}</p>
                        @endif
                    </div>
                    <button onclick="tambahKeKeranjang('{{ $produk->id_produk }}', '{{ $produk->nama_produk }}', {{ $produk->getHargaNet() }})" 
                        class="mt-3 w-full bg-emerald-500 text-white text-sm py-2 rounded hover:bg-emerald-600 transition font-bold">
                        <i class="fa-solid fa-cart-plus mr-1"></i> Pesan
                    </button>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Keranjang Belanja -->
        <div class="bg-white p-6 rounded-lg shadow h-fit lg:sticky lg:top-4">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">
                <i class="fa-solid fa-shopping-cart text-emerald-500 mr-2"></i> Pesanan Saya
            </h2>

            <div id="keranjang-list" class="space-y-3 divide-y divide-gray-100 max-h-60 overflow-y-auto pr-1">
                <p class="text-gray-400 text-center text-sm py-8">Belum ada pesanan</p>
            </div>

            <div class="border-t pt-4 mt-4">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-600 font-semibold">Total:</span>
                    <span id="grand-total" class="text-2xl font-black text-emerald-600">Rp 0</span>
                </div>
                <button onclick="showCheckoutModal()" id="btn-pesan" class="w-full bg-emerald-600 text-white font-bold py-2.5 rounded shadow hover:bg-emerald-700 transition disabled:bg-gray-300" disabled>
                    <i class="fa-solid fa-paper-plane mr-2"></i> Pesan Sekarang
                </button>
                <p class="text-[10px] text-gray-400 text-center mt-2">Pesanan Anda akan langsung diproses</p>
            </div>
        </div>
    </main>

    <!-- Modal Sukses -->
    <!-- Modal Checkout -->
    <div id="checkout-modal" class="hidden fixed inset-0 bg-black/60 items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
            <h3 class="text-xl font-black text-gray-900 mb-4">Konfirmasi Pesanan</h3>
            
            <div class="bg-gray-50 rounded-xl p-4 mb-4 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Nama Pesanan:</span>
                    <span id="checkout-nama-pesanan" class="font-bold text-gray-800"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Harga:</span>
                    <span id="checkout-total-harga" class="font-bold text-green-600"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Nomor Pesanan:</span>
                    <span id="checkout-nomor-pesanan" class="font-bold text-gray-800"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Pembayaran:</span>
                    <span class="font-bold text-gray-800">QRIS</span>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat Rumah</label>
                    <textarea id="alamat" name="alamat" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Masukkan alamat lengkap Anda"></textarea>
                </div>
                <div>
                    <label for="nomor_hp" class="block text-sm font-medium text-gray-700">Nomor HP</label>
                    <input type="text" id="nomor_hp" name="nomor_hp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Masukkan nomor HP Anda">
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button onclick="hideCheckoutModal()" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-300 transition">Batal</button>
                <button onclick="submitCheckout()" class="bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-emerald-700 transition">Konfirmasi Pesanan</button>
            </div>
        </div>
    </div>

    <!-- Modal QRIS -->
    <div id="qrisModal" class="hidden fixed inset-0 bg-black/60 z-50 items-center justify-center p-4">
        <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-sm text-center">
            <h2 class="text-xl font-bold mb-2 text-gray-800">Scan QRIS Pembayaran</h2>
            <p class="text-sm text-gray-600 mb-4">Total: <span id="qris-modal-total" class="font-bold text-emerald-600"></span></p>

            <div class="flex justify-center mb-4 relative">
                <div id="qris-loader" class="absolute inset-0 flex items-center justify-center bg-white/80 rounded-lg hidden">
                    <i class="fa-solid fa-spinner fa-spin text-3xl text-emerald-500"></i>
                </div>
                <img id="qris-image" src="" alt="QRIS Code" class="w-64 h-64 border rounded-lg shadow-sm">
            </div>

            <button onclick="prosesPembayaranQRIS()" id="btn-proses-qris" class="w-full bg-emerald-600 text-white py-2 mb-2 rounded-lg font-bold hover:bg-emerald-700 flex justify-center items-center gap-2">
                <i class="fa-solid fa-check-circle"></i> Cek Status Pembayaran
            </button>
            <button onclick="tutupQris()" class="w-full bg-red-500 text-white py-2 rounded-lg font-bold hover:bg-red-600">Batal</button>
        </div>
    </div>

    <!-- Modal Sukses -->
    <div id="success-modal" class="hidden fixed inset-0 bg-black/60 items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <i class="fa-solid fa-circle-check text-green-600 text-4xl"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 mb-1">Pesanan Berhasil!</h3>
            <p class="text-sm text-gray-500 mb-4">Terima kasih, pesanan Anda sedang diproses.</p>
            <div class="bg-gray-50 rounded-xl p-4 mb-4 space-y-2 text-left border border-gray-100 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">ID Transaksi:</span>
                    <span id="customer-trx-id" class="font-bold text-gray-800"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total:</span>
                    <span id="customer-total" class="font-bold text-green-600"></span>
                </div>
            </div>
            <button onclick="selesaiPesan()" class="w-full bg-emerald-600 text-white font-bold py-3 rounded-xl hover:bg-emerald-700 transition shadow-md">
                <i class="fa-solid fa-check mr-2"></i> Selesai
            </button>
        </div>
    </div>

    <script>
        let keranjang = [];
        let currentTrxId = '';
        let currentTotal = 0;

        function formatRupiah(angka) {
            return 'Rp ' + parseFloat(angka).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        function tambahKeKeranjang(id, nama, harga) {
            let index = keranjang.findIndex(item => item.id_produk === id);
            if (index !== -1) {
                keranjang[index].qty++;
            } else {
                keranjang.push({ id_produk: id, nama_produk: nama, harga: harga, qty: 1 });
            }
            updateTampilan();
        }

        function updateTampilan() {
            let list = document.getElementById('keranjang-list');
            let total = document.getElementById('grand-total');
            let btn = document.getElementById('btn-pesan');

            if (keranjang.length === 0) {
                list.innerHTML = '<p class="text-gray-400 text-center text-sm py-8">Belum ada pesanan</p>';
                total.innerText = 'Rp 0';
                btn.disabled = true;
                return;
            }

            btn.disabled = false;
            let html = '';
            let grandTotal = 0;

            keranjang.forEach((item, i) => {
                let subtotal = item.harga * item.qty;
                grandTotal += subtotal;
                html += `
                <div class="flex justify-between items-center py-2 text-sm">
                    <div>
                        <h4 class="font-bold text-gray-700">${item.nama_produk}</h4>
                        <p class="text-xs text-gray-400">${item.qty} x ${formatRupiah(item.harga)}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-600">${formatRupiah(subtotal)}</span>
                        <button onclick="hapusItem(${i})" class="text-red-400 hover:text-red-600 text-xs">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>`;
            });

            list.innerHTML = html;
            total.innerText = formatRupiah(grandTotal);
        }

        function hapusItem(index) {
            keranjang.splice(index, 1);
            updateTampilan();
        }

        function showCheckoutModal() {
            let grandTotal = keranjang.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            let orderNames = keranjang.map(item => item.nama_produk).join(', ');
            let orderNumber = 'TRX-' + new Date().getTime();

            document.getElementById('checkout-nama-pesanan').innerText = orderNames;
            document.getElementById('checkout-total-harga').innerText = formatRupiah(grandTotal);
            document.getElementById('checkout-nomor-pesanan').innerText = orderNumber;

            document.getElementById('checkout-modal').classList.remove('hidden');
            document.getElementById('checkout-modal').classList.add('flex');
        }

        function hideCheckoutModal() {
            document.getElementById('checkout-modal').classList.add('hidden');
            document.getElementById('checkout-modal').classList.remove('flex');
        }

        function submitCheckout() {
            let namaPembeli = "{{ session('nama', 'Customer') }}";
            let alamat = document.getElementById('alamat').value.trim();
            let nomor_hp = document.getElementById('nomor_hp').value.trim();
            let nomor_pesanan = document.getElementById('checkout-nomor-pesanan').innerText;

            if (!alamat || !nomor_hp) {
                alert('Alamat dan Nomor HP harus diisi.');
                return;
            }

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
                    alamat: alamat,
                    nomor_hp: nomor_hp
                })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || 'Gagal menyimpan pesanan.');
                }
                return data;
            })
            .then(res => {
                if (res.status === 'success') {
                    currentTrxId = res.id_transaksi;
                    currentTotal = res.total_tagihan;

                    hideCheckoutModal();
                    tampilkanModalQRIS();
                } else {
                    alert(res.message || 'Gagal menyimpan pesanan.');
                }
            })
            .catch(err => {
                alert(err.message || 'Terjadi kesalahan. Silakan coba lagi.');
            });
        }

        function tampilkanModalQRIS() {
            document.getElementById('qris-modal-total').innerText = formatRupiah(currentTotal);
            let qrData = `QRIS-TRX-${currentTrxId}-TOTAL-${currentTotal}`;
            document.getElementById('qris-image').src = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrData)}`;

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

            btnProses.disabled = true;
            btnProses.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
            qrisLoader.classList.remove('hidden');

            setTimeout(() => {
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
                    if (!res.ok) {
                        throw new Error(data.message || 'Gagal memproses pembayaran.');
                    }
                    return data;
                })
                .then(res => {
                    btnProses.disabled = false;
                    btnProses.innerHTML = '<i class="fa-solid fa-check-circle"></i> Cek Status Pembayaran';
                    qrisLoader.classList.add('hidden');

                    if (res.status === 'success') {
                        tutupQris();
                        document.getElementById('customer-trx-id').innerText = currentTrxId;
                        document.getElementById('customer-total').innerText = formatRupiah(currentTotal);

                        document.getElementById('success-modal').classList.remove('hidden');
                        document.getElementById('success-modal').classList.add('flex');
                    } else {
                        alert(res.message || 'Pembayaran gagal.');
                    }
                })
                .catch(err => {
                    btnProses.disabled = false;
                    btnProses.innerHTML = '<i class="fa-solid fa-check-circle"></i> Cek Status Pembayaran';
                    qrisLoader.classList.add('hidden');
                    alert(err.message || 'Terjadi kesalahan saat pembayaran.');
                });
            }, 1500);
        }

        function selesaiPesan() {
            keranjang = [];
            updateTampilan();
            document.getElementById('success-modal').classList.add('hidden');
            document.getElementById('success-modal').classList.remove('flex');
        }
    </script>
</body>
</html>