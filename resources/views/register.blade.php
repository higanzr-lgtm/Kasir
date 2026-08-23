<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar - Warung Kopi Kita</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        * { font-family: 'Inter', sans-serif; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 p-4">
    
    <div class="fixed inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>

    <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl w-full max-w-md p-6 sm:p-8 animate-fade-in-up">
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl shadow-emerald-200/50 transform hover:scale-105 transition-transform">
                <i class="fa-solid fa-user-plus text-white text-3xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-900">Buat Akun Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar untuk mulai pesan online</p>
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

        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
            <div class="flex items-center gap-2 mb-1.5">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                <span class="font-bold">Perbaiki data berikut:</span>
            </div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('register.proses') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">
                        <i class="fa-solid fa-user text-emerald-500 mr-1"></i> Nama
                    </label>
                    <input type="text" name="nama" required 
                        class="w-full border-2 border-gray-200 rounded-2xl px-4 py-3 text-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200 transition-all outline-none" 
                        placeholder="Nama Anda" value="{{ old('nama') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">
                        <i class="fa-solid fa-at text-emerald-500 mr-1"></i> Username
                    </label>
                    <input type="text" name="username" required 
                        class="w-full border-2 border-gray-200 rounded-2xl px-4 py-3 text-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200 transition-all outline-none" 
                        placeholder="username" value="{{ old('username') }}">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                    <i class="fa-solid fa-envelope text-emerald-500 mr-1"></i> Email
                </label>
                <input type="email" name="email" required 
                    class="w-full border-2 border-gray-200 rounded-2xl px-4 py-3 text-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200 transition-all outline-none" 
                    placeholder="contoh@email.com" value="{{ old('email') }}">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                    <i class="fa-solid fa-lock text-emerald-500 mr-1"></i> Password
                </label>
                <input type="password" name="password" required 
                    class="w-full border-2 border-gray-200 rounded-2xl px-4 py-3 text-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200 transition-all outline-none" 
                    placeholder="Minimal 6 karakter">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                    <i class="fa-solid fa-check-double text-emerald-500 mr-1"></i> Konfirmasi Password
                </label>
                <input type="password" name="password_confirmation" required 
                    class="w-full border-2 border-gray-200 rounded-2xl px-4 py-3 text-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200 transition-all outline-none" 
                    placeholder="Ulangi password">
            </div>

            <button type="submit" 
                class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-extrabold py-3.5 rounded-2xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-200/50 active:scale-[0.98] flex items-center justify-center gap-2">
                <i class="fa-solid fa-user-plus"></i>
                Daftar & Kirim OTP
            </button>
        </form>

        <div class="text-center mt-5">
            <p class="text-sm text-gray-500">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-700 font-bold transition-all">Masuk di sini</a>
            </p>
        </div>
    </div>

</body>
</html>