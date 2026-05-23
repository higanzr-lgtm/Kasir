<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kode OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 24px;">
    <div style="max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h2 style="color: #059669; margin: 0 0 8px;">Warung Kopi Kita</h2>
        <p style="color: #6b7280; margin: 0 0 24px; font-size: 14px;">Verifikasi akun registrasi Anda</p>

        <p style="color: #374151; font-size: 15px;">Halo <strong>{{ $nama }}</strong>,</p>
        <p style="color: #374151; font-size: 15px; line-height: 1.6;">
            Terima kasih telah mendaftar. Gunakan kode OTP berikut untuk menyelesaikan verifikasi email. Kode berlaku selama <strong>10 menit</strong>.
        </p>

        <div style="text-align: center; margin: 32px 0;">
            <span style="display: inline-block; background: #ecfdf5; color: #047857; font-size: 32px; font-weight: bold; letter-spacing: 8px; padding: 16px 32px; border-radius: 8px; border: 2px dashed #6ee7b7;">
                {{ $otp }}
            </span>
        </div>

        <p style="color: #9ca3af; font-size: 12px; line-height: 1.5;">
            Jangan bagikan kode ini kepada siapapun. Jika Anda tidak melakukan registrasi, abaikan email ini.
        </p>
    </div>
</body>
</html>
