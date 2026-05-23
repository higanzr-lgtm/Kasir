<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Warung Kopi Kita</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <div class="bg-green-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 shadow">
                <i class="fa-solid fa-lock-open text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Reset Password</h2>
            <p class="text-sm text-gray-500 mt-1">Buat password baru untuk akun Anda</p>
        </div>

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('lupa.password.reset.proses') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Password Baru</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-key"></i>
                    </span>
                    <input type="password" name="password" required minlength="6"
                        class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-green-500"
                        placeholder="Minimal 6 karakter">
                </div>
                @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Konfirmasi Password Baru</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-check-double"></i>
                    </span>
                    <input type="password" name="password_confirmation" required minlength="6"
                        class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-green-500"
                        placeholder="Ulangi password baru">
                </div>
            </div>

            <button type="submit" class="w-full bg-green-500 text-white font-bold py-2.5 rounded-lg hover:bg-green-600 transition shadow">
                <i class="fa-solid fa-save mr-2"></i> Simpan Password Baru
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="text-sm text-gray-500">
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Login
                </a>
            </p>
        </div>
    </div>

</body>
</html>