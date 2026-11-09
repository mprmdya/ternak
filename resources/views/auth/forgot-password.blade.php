<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | Pemantauan Peternakan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif; min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 50%, #F0F4F8 100%); padding: 20px;
        }
        .auth-container { width: 100%; max-width: 420px; }
        .auth-logo { text-align: center; margin-bottom: 32px; }
        .auth-logo .icon {
            width: 56px; height: 56px; border-radius: 16px;
            background: linear-gradient(135deg, #16A34A, #22C55E);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 24px; color: #fff; margin-bottom: 12px;
            box-shadow: 0 4px 16px rgba(34,197,94,0.3);
        }
        .auth-logo h1 { font-size: 1.6rem; font-weight: 800; color: #1A2533; }
        .auth-card {
            background: #fff; border-radius: 20px; padding: 36px 32px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08); border: 1px solid #E2E8F0;
        }
        .auth-card h2 { font-size: 1.35rem; font-weight: 700; color: #1A2533; margin-bottom: 6px; }
        .auth-card .subtitle { font-size: 0.88rem; color: #4A6680; margin-bottom: 28px; line-height: 1.5; }
        .form-group { margin-bottom: 24px; }
        .form-group label { display: block; font-size: 0.82rem; font-weight: 600; color: #374151; margin-bottom: 7px; }
        .form-group input {
            width: 100%; padding: 12px 14px; border: 1px solid #D1D5DB;
            border-radius: 12px; font-size: 0.92rem; font-family: inherit;
            background: #F9FAFB; transition: border-color 0.2s, box-shadow 0.2s; color: #1A2533;
        }
        .form-group input:focus { outline: none; border-color: #22C55E; box-shadow: 0 0 0 3px rgba(34,197,94,0.15); }
        .btn-submit {
            width: 100%; padding: 13px; border: none; border-radius: 12px;
            background: linear-gradient(135deg, #16A34A, #22C55E);
            color: #fff; font-size: 0.95rem; font-weight: 700; cursor: pointer;
            transition: transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(34,197,94,0.3);
        }
        .btn-submit:hover { transform: translateY(-1px); }
        .auth-footer { text-align: center; margin-top: 20px; font-size: 0.85rem; color: #4A6680; }
        .auth-footer a { color: #16A34A; font-weight: 600; text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; }
        .alert-error { background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; font-size: 0.85rem; }
        .alert-success { background: #DCFCE7; border: 1px solid #86EFAC; color: #15803D; border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-logo">
        <div class="icon"><i class="fas fa-leaf"></i></div>
        <h1>FarmModern</h1>
    </div>

    <div class="auth-card">
        <h2>Lupa Password?</h2>
        <p class="subtitle">Masukkan alamat email Anda dan kami akan mengirimkan link untuk mereset password.</p>

        @if ($errors->any())
            <div class="alert-error"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
        @endif

        @if (session('status'))
            <div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Terdaftar</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com" required autofocus>
            </div>
            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> Kirim Link Reset
            </button>
        </form>

        <div class="auth-footer">
            <a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Kembali ke Login</a>
        </div>
    </div>
</div>

</body>
</html>
