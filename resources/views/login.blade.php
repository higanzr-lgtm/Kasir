<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Masuk - Warung Kopi Kita</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        * { font-family: 'Inter', sans-serif; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out; }
        .animate-float { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 p-4">
    
    <!-- Background Pattern -->
    <div class="fixed inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>

    <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl w-full max-w-md p-6 sm:p-8 animate-fade-in-up relative">
        <!-- Logo -->
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl shadow-emerald-200/50 transform hover:scale-105 transition-transform">
                <i class="fa-solid fa-mug-hot text-white text-3xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900">Selamat Datang</h2>
            <p class="text-sm text-gray-500 mt-1">Warung Kopi Kita - Pesan Online</p>
        </div>

        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500"></i>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-red-500"></i>
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('login.proses') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                    <i class="fa-solid fa-envelope text-emerald-500 mr-1"></i> Email
                </label>
                <input type="email" name="email" required 
                    class="w-full border-2 border-gray-200 rounded-2xl px-4 py-3 text-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200 transition-all outline-none" 
                    placeholder="contoh@email.com">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                    <i class="fa-solid fa-lock text-emerald-500 mr-1"></i> Password
                </label>
                <input type="password" name="password" required 
                    class="w-full border-2 border-gray-200 rounded-2xl px-4 py-3 text-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200 transition-all outline-none" 
                    placeholder="••••••••">
            </div>

            <button type="submit" 
                class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-extrabold py-3.5 rounded-2xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-200/50 active:scale-[0.98] flex items-center justify-center gap-2">
                <i class="fa-solid fa-right-to-bracket"></i>
                Masuk
            </button>
        </form>

        <div class="flex justify-between items-center mt-5">
            <p class="text-sm text-gray-500">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-700 font-bold transition-all">Daftar</a>
            </p>
            <a href="{{ route('lupa.password') }}" class="text-sm text-orange-500 hover:text-orange-600 font-semibold transition-all flex items-center gap-1">
                <i class="fa-solid fa-unlock-alt"></i>
                Lupa Password?
            </a>
        </div>
    </div>

</body>
</html>