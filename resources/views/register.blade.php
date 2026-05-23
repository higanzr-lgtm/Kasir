<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Kasir POS</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <div class="bg-green-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 shadow">
                <i class="fa-solid fa-user-plus text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Registrasi Akun Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Daftarkan diri Anda untuk mengakses sistem</p>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 text-sm">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
            {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
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
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" required class="w-full border rounded-lg p-2 text-sm focus:outline-blue-500" placeholder="Nama Anda" value="{{ old('nama') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Username</label>
                    <input type="text" name="username" required class="w-full border rounded-lg p-2 text-sm focus:outline-blue-500" placeholder="username" value="{{ old('username') }}">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" required class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-blue-500" placeholder="contoh@email.com" value="{{ old('email') }}">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-key"></i>
                    </span>
                    <input type="password" name="password" required class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-blue-500" placeholder="Minimal 6 karakter">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Konfirmasi Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-check-double"></i>
                    </span>
                    <input type="password" name="password_confirmation" required class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-blue-500" placeholder="Ulangi password">
                </div>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white font-bold py-2.5 rounded-lg hover:bg-green-700 transition shadow">
                <i class="fa-solid fa-user-plus mr-2"></i> Daftar & Kirim OTP ke Email
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="text-sm text-gray-500">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Masuk di sini</a>
            </p>
        </div>
    </div>

</body>
</html>