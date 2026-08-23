<!-- ====== MODAL DETAIL PESANAN OWNER ====== -->
<div id="owner-detail-modal" class="hidden fixed inset-0 bg-black/60 z-50 items-center justify-center p-3 sm:p-4 modal-overlay">
    <div class="bg-white rounded-3xl p-5 sm:p-6 max-w-lg w-full shadow-2xl overflow-y-auto max-h-[90vh] animate-scale-in">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-600 rounded-2xl flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-receipt text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-gray-900">Detail Pesanan</h3>
                    <p id="owner-detail-status" class="text-[10px] font-semibold"></p>
                </div>
            </div>
            <button onclick="hideOwnerDetailPesanan()" class="w-8 h-8 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-all cursor-pointer">
                <i class="fa-solid fa-xmark text-gray-500"></i>
            </button>
        </div>

        <!-- Info Pesanan -->
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-4 mb-4 text-sm border border-indigo-100 space-y-2.5">
            <div class="flex justify-between">
                <span class="text-gray-500">ID Transaksi</span>
                <span id="owner-detail-id" class="font-bold text-gray-800 font-mono text-xs"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">No. Pesanan</span>
                <span id="owner-detail-nomor" class="font-bold text-gray-800"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal</span>
                <span id="owner-detail-tanggal" class="font-bold text-gray-800 text-xs"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Nama Customer</span>
                <span id="owner-detail-customer" class="font-bold text-gray-800"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Nomor HP</span>
                <span id="owner-detail-hp" class="font-bold text-gray-800"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Metode Bayar</span>
                <span id="owner-detail-metode" class="font-bold text-gray-800"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Status</span>
                <span id="owner-detail-statusbayar" class="font-bold"></span>
            </div>
        </div>

        <!-- Daftar Item -->
        <div class="mb-4">
            <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                <i class="fa-solid fa-box text-indigo-500"></i> Item Pesanan
            </h4>
            <div id="owner-detail-items" class="space-y-2 divide-y divide-gray-100 bg-gray-50 rounded-2xl p-3">
            </div>
        </div>

        <!-- Total -->
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-4 text-white mb-4">
            <div class="flex justify-between items-center">
                <span class="text-indigo-100 text-sm font-medium">Total Pembayaran</span>
                <span id="owner-detail-total" class="text-xl font-extrabold"></span>
            </div>
        </div>

        <!-- Deskripsi (dari alamat) -->
        <div id="owner-detail-deskripsi-area" class="bg-gray-50 rounded-2xl p-3 text-xs text-gray-600 mb-4 hidden">
            <div class="flex gap-2">
                <i class="fa-solid fa-note-sticky text-indigo-400 mt-0.5"></i>
                <span id="owner-detail-deskripsi"></span>
            </div>
        </div>

        <button onclick="hideOwnerDetailPesanan()" class="w-full bg-gray-100 text-gray-600 font-bold py-3 rounded-2xl hover:bg-gray-200 transition-all active:scale-95">
            Tutup
        </button>
    </div>
</div>

<script>
// Data transaksi untuk Owner
let ownerTransaksiData = [];

function showOwnerDetailPesanan(idTransaksi) {
    // Cari data dari daftar yang sudah ada
    let trx = window.ownerTransaksiData?.find(t => t.id_transaksi === idTransaksi);
    if (!trx) {
        // Jika tidak ada, fetch dari API
        fetch('/owner/transaksi/' + idTransaksi)
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success' && res.data) {
                trx = res.data;
                tampilkanDetailOwner(trx);
            } else {
                alert('Gagal memuat detail!');
            }
        })
        .catch(() => alert('Gagal memuat detail!'));
        return;
    }
    tampilkanDetailOwner(trx);
}

function tampilkanDetailOwner(data) {
    document.getElementById('owner-detail-id').innerText = data.id_transaksi || '-';
    document.getElementById('owner-detail-nomor').innerText = data.nomor_pesanan || '-';
    
    let tgl = data.created_at ? new Date(data.created_at).toLocaleString('id-ID', { 
        year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' 
    }) : '-';
    document.getElementById('owner-detail-tanggal').innerText = tgl;
    document.getElementById('owner-detail-customer').innerText = data.nama_pembeli || '-';
    document.getElementById('owner-detail-hp').innerText = data.nomor_hp || '-';
    document.getElementById('owner-detail-total').innerText = formatRupiah(data.total_bayar || 0);
    
    // Metode bayar
    let metode = data.pembayaran ? data.pembayaran.metode_bayar : '-';
    document.getElementById('owner-detail-metode').innerText = metode;
    
    // Status
    let statusEl = document.getElementById('owner-detail-statusbayar');
    if (data.status_pembayaran === 'Lunas') {
        statusEl.innerHTML = '<span class="text-emerald-600"><i class="fa-solid fa-circle-check"></i> Berhasil</span>';
        document.getElementById('owner-detail-status').innerHTML = '<span class="text-emerald-600">✅ Pembayaran berhasil</span>';
    } else {
        statusEl.innerHTML = '<span class="text-yellow-600"><i class="fa-solid fa-spinner"></i> Proses</span>';
        document.getElementById('owner-detail-status').innerHTML = '<span class="text-yellow-600">⏳ Menunggu pembayaran</span>';
    }

    // Items
    let itemsHtml = '';
    let detailTransaksis = data.detail_transaksis || data.items || [];
    if (detailTransaksis.length > 0) {
        detailTransaksis.forEach(item => {
            let nama = item.produk?.nama_produk || item.nama_produk || 'N/A';
            let qty = item.qty || 1;
            let subtotal = parseFloat(item.subtotal) || 0;
            let hargaSatuan = qty > 0 ? subtotal / qty : 0;
            itemsHtml += `<div class="flex justify-between items-center py-1.5 text-sm">
                <div>
                    <span class="font-semibold text-gray-800">${nama}</span>
                    <span class="text-gray-400 ml-1">x${qty}</span>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-400">@${formatRupiah(hargaSatuan)}</span>
                    <span class="font-bold text-gray-800 ml-2">${formatRupiah(subtotal)}</span>
                </div>
            </div>`;
        });
    }
    document.getElementById('owner-detail-items').innerHTML = itemsHtml || '<p class="text-gray-400 text-sm">Tidak ada item</p>';

    // Deskripsi (simpan di field alamat)
    let deskripsiArea = document.getElementById('owner-detail-deskripsi-area');
    let deskripsi = data.alamat || '';
    if (deskripsi && deskripsi !== 'Tidak ada deskripsi') {
        document.getElementById('owner-detail-deskripsi').innerText = deskripsi;
        deskripsiArea.classList.remove('hidden');
    } else {
        deskripsiArea.classList.add('hidden');
    }

    // Tampilkan modal
    document.getElementById('owner-detail-modal').classList.remove('hidden');
    document.getElementById('owner-detail-modal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function hideOwnerDetailPesanan() {
    document.getElementById('owner-detail-modal').classList.add('hidden');
    document.getElementById('owner-detail-modal').classList.remove('flex');
    document.body.style.overflow = '';
}

// Tutup modal saat klik di luar
document.addEventListener('click', function(e) {
    const modal = document.getElementById('owner-detail-modal');
    if (e.target === modal) hideOwnerDetailPesanan();
});
</script>