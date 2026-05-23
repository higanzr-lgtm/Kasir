<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Kopi Kita - Beranda</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans min-h-screen">

    <nav class="bg-emerald-700 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold"><i class="fa-solid fa-store mr-2"></i> Warung Kopi Kita</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="bg-white text-emerald-700 px-4 py-1.5 rounded text-sm font-bold hover:bg-gray-100 transition">
                    <i class="fa-solid fa-right-to-bracket mr-1"></i> Login
                </a>
                <a href="{{ route('register') }}" class="bg-emerald-600 text-white px-4 py-1.5 rounded text-sm font-bold hover:bg-emerald-500 transition border border-emerald-400">
                    <i class="fa-solid fa-user-plus mr-1"></i> Daftar
                </a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-6">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Selamat Datang di Warung Kopi Kita</h2>
            <p class="text-gray-500 mt-1">Silakan lihat menu kami. Login untuk melakukan pemesanan.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($produks as $produk)
            <div class="bg-white rounded-xl shadow overflow-hidden hover:shadow-lg transition">
                <div class="w-full h-40 overflow-hidden bg-gray-200">
                    <img src="{{ asset('images/menu/' . $produk->foto) }}" 
                        alt="{{ $produk->nama_produk }}" 
                        class="w-full h-full object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-800">{{ $produk->nama_produk }}</h3>
                    @if($produk->diskon)
                        <p class="text-xs text-red-500 line-through">Rp {{ number_format($produk->harga_normal, 0, ',', '.') }}</p>
                        <p class="text-lg font-bold text-green-600">Rp {{ number_format($produk->getHargaNet(), 0, ',', '.') }}</p>
                        <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded font-bold">{{ $produk->diskon->nama_diskon }}</span>
                    @else
                        <p class="text-lg font-bold text-gray-800">Rp {{ number_format($produk->harga_normal, 0, ',', '.') }}</p>
                    @endif
                    <a href="{{ route('register') }}" class="mt-3 block w-full bg-emerald-500 text-white text-sm py-2 rounded hover:bg-emerald-600 transition font-bold text-center">
                        <i class="fa-solid fa-cart-plus mr-1"></i> Pesan (Daftar Dulu)
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </main>

    <footer class="bg-gray-800 text-gray-400 text-center p-4 mt-8 text-xs">
        &copy; 2026 Warung Kopi Kita. All rights reserved.
    </footer>

</body>
</html>