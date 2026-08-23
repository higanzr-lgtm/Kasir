<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\UserKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:user_karyawans,username',
            'email' => 'required|email|max:100|unique:user_karyawans,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $lastId = UserKaryawan::max('id_user') ?? 0;
        $newId = $lastId + 1;

        $isFirstUser = UserKaryawan::count() === 0;
        $role = $isFirstUser ? 'Owner' : 'Customer';

        $user = UserKaryawan::create([
            'id_user' => $newId,
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $role,
            'password' => Hash::make($request->password),
        ]);

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            Mail::to($user->email)->send(new OtpMail($user->nama, $otp));
        } catch (\Exception $e) {
            $user->delete();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengirim email OTP. Periksa pengaturan MAIL di file .env Anda.');
        }

        session([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10)->timestamp,
            'otp_user_id' => $user->id_user,
            'otp_user_email' => $user->email,
            'otp_user_nama' => $user->nama,
            'otp_user_role' => $user->role,
            'otp_purpose' => 'register',
        ]);

        $maskedEmail = $this->maskEmail($user->email);

        return redirect()->route('otp.form')
            ->with('success', 'Kode OTP telah dikirim ke email ' . $maskedEmail . '. Silakan cek inbox Anda.');
    }

    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = UserKaryawan::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Email atau Password salah!');
        }

        if (!$user->email_verified_at) {
            return redirect()->back()->with('error', 'Email belum diverifikasi. Selesaikan verifikasi OTP saat registrasi atau daftar ulang.');
        }

        session([
            'isLogin' => true,
            'id_user' => $user->id_user,
            'nama' => $user->nama,
            'role' => $user->role,
        ]);

        if ($user->role === 'Owner') {
            return redirect()->route('owner.dashboard');
        }
        if ($user->role === 'Customer') {
            return redirect()->route('customer.dashboard');
        }
        if ($user->role === 'Kurir') {
            return redirect()->route('kurir.dashboard');
        }

        return redirect()->route('kasir.dashboard');
    }

    public function showOtpForm()
    {
        if (!session('otp') || session('otp_purpose') !== 'register') {
            return redirect()->route('register')->with('error', 'Sesi verifikasi tidak valid. Silakan daftar ulang.');
        }

        return view('otp');
    }

    public function verifikasiOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if (!session('otp') || session('otp_purpose') !== 'register') {
            return redirect()->route('register')->with('error', 'Sesi OTP telah berakhir. Silakan daftar ulang.');
        }

        $otpExpiresAt = session('otp_expires_at');
        if ($otpExpiresAt && now()->timestamp > $otpExpiresAt) {
            session()->forget(['otp', 'otp_expires_at', 'otp_user_id', 'otp_user_email', 'otp_user_nama', 'otp_user_role', 'otp_purpose']);

            return redirect()->route('register')->with('error', 'Kode OTP sudah kedaluwarsa. Silakan daftar ulang.');
        }

        if ($request->otp !== session('otp')) {
            return redirect()->back()->with('error', 'Kode OTP yang Anda masukkan salah!');
        }

        $user = UserKaryawan::find(session('otp_user_id'));

        if (!$user) {
            return redirect()->route('register')->with('error', 'Akun tidak ditemukan. Silakan daftar ulang.');
        }

        $user->update(['email_verified_at' => now()]);

        session()->forget(['otp', 'otp_expires_at', 'otp_user_id', 'otp_user_email', 'otp_user_nama', 'otp_user_role', 'otp_purpose']);

        return redirect()->route('login')->with('success', 'Email berhasil diverifikasi! Silakan login dengan akun Anda.');
    }

    public function logout()
    {
        session()->flush();

        return redirect()->route('login');
    }

    // ========== LUPA PASSWORD ==========

    public function showLupaPassword()
    {
        return view('lupa_password');
    }

    public function kirimOtpLupaPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:user_karyawans,email',
        ]);

        $user = UserKaryawan::where('email', $request->email)->first();

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            Mail::to($user->email)->send(new OtpMail($user->nama, $otp));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengirim email OTP. Periksa pengaturan MAIL di file .env Anda.');
        }

        session([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10)->timestamp,
            'otp_user_id' => $user->id_user,
            'otp_user_email' => $user->email,
            'otp_user_nama' => $user->nama,
            'otp_purpose' => 'reset_password',
        ]);

        $maskedEmail = $this->maskEmail($user->email);

        return redirect()->route('lupa.password.verifikasi')
            ->with('success', 'Kode OTP telah dikirim ke ' . $maskedEmail . '. Silakan cek inbox Anda.');
    }

    public function showVerifikasiLupaPassword()
    {
        if (!session('otp') || session('otp_purpose') !== 'reset_password') {
            return redirect()->route('lupa.password')->with('error', 'Sesi tidak valid. Silakan mulai ulang.');
        }

        return view('verifikasi_lupa_password');
    }

    public function verifikasiOtpLupaPassword(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        if (!session('otp') || session('otp_purpose') !== 'reset_password') {
            return redirect()->route('lupa.password')->with('error', 'Sesi OTP telah berakhir. Silakan mulai ulang.');
        }

        if (session('otp_expires_at') && now()->timestamp > session('otp_expires_at')) {
            session()->forget(['otp', 'otp_expires_at', 'otp_user_id', 'otp_user_email', 'otp_user_nama', 'otp_purpose']);

            return redirect()->route('lupa.password')->with('error', 'Kode OTP sudah kedaluwarsa. Silakan mulai ulang.');
        }

        if ($request->otp !== session('otp')) {
            return redirect()->back()->with('error', 'Kode OTP yang Anda masukkan salah!');
        }

        // OTP benar, tandai sesi bahwa OTP sudah diverifikasi
        session(['otp_verified' => true]);

        return redirect()->route('lupa.password.reset')
            ->with('success', 'OTP berhasil diverifikasi! Silakan buat password baru Anda.');
    }

    public function showResetPassword()
    {
        if (!session('otp_verified') || session('otp_purpose') !== 'reset_password') {
            return redirect()->route('lupa.password')->with('error', 'Sesi tidak valid. Silakan mulai ulang dari awal.');
        }

        return view('reset_password');
    }

    public function resetPassword(Request $request)
    {
        if (!session('otp_verified') || !session('otp_user_id') || session('otp_purpose') !== 'reset_password') {
            return redirect()->route('lupa.password')->with('error', 'Sesi tidak valid. Silakan mulai ulang.');
        }

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = UserKaryawan::find(session('otp_user_id'));

        if (!$user) {
            return redirect()->route('lupa.password')->with('error', 'Akun tidak ditemukan.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        session()->forget(['otp', 'otp_expires_at', 'otp_user_id', 'otp_user_email', 'otp_user_nama', 'otp_purpose', 'otp_verified']);

        return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login dengan password baru Anda.');
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        $visible = substr($local, 0, min(2, strlen($local)));

        return $visible . str_repeat('*', max(strlen($local) - 2, 1)) . '@' . $domain;
    }
}