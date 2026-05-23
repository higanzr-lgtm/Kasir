<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - Sistem Kasir POS</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <div class="bg-green-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 shadow">
                <i class="fa-solid fa-envelope-circle-check text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Verifikasi Email</h2>
            <p class="text-sm text-gray-500 mt-1">Masukkan kode 6 digit yang dikirim ke email Anda saat registrasi</p>
            @if(session('otp_user_email'))
            <p class="text-xs text-emerald-600 font-semibold mt-2">{{ session('otp_user_email') }}</p>
            @endif
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

        <form action="{{ route('otp.verifikasi') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-3 text-center">Kode OTP (6 digit)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-key"></i>
                    </span>
                    <input type="text" name="otp" required maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                        class="w-full pl-10 pr-4 py-3 border rounded-lg text-sm text-center tracking-[10px] font-bold text-xl focus:outline-green-600"
                        placeholder="000000" autofocus>
                </div>
                @error('otp')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-green-600 text-white font-bold py-2.5 rounded-lg hover:bg-green-700 transition shadow">
                <i class="fa-solid fa-check-circle mr-2"></i> Verifikasi Email
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="{{ route('register') }}" class="text-sm text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Registrasi
            </a>
        </div>

        <p class="text-[11px] text-gray-400 text-center mt-4">
            Kode berlaku 10 menit. Cek folder spam jika email belum masuk.
        </p>
    </div>
</body>
</html>
