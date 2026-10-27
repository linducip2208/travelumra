<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Admin Panel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8ecf8 30%, #f8fafc 60%, #eef2ff 100%);
            padding: 20px;
        }
        .card {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 20px;
            padding: 40px 32px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.06), 0 16px 48px rgba(79,70,229,0.08);
        }
        .logo {
            text-align: center;
            margin-bottom: 24px;
        }
        .logo svg { width: 48px; height: 48px; }
        h1 {
            font-size: 22px;
            font-weight: 700;
            color: #111827;
            text-align: center;
            margin-bottom: 24px;
        }
        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            font-size: 14px;
            color: #111827;
            background: #fff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        input[type="email"]:focus, input[type="password"]:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }
        .field { margin-bottom: 16px; }
        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .checkbox-row input { accent-color: #6366f1; width: 16px; height: 16px; }
        .checkbox-row label { font-size: 13px; color: #6b7280; margin: 0; }
        button {
            width: 100%;
            padding: 13px 24px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(79,70,229,0.3);
            transition: transform 0.15s, box-shadow 0.15s;
        }
        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(79,70,229,0.4);
        }
        .error {
            background: #fef2f2;
            color: #dc2626;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        @media (max-width: 480px) {
            .card { padding: 28px 20px; border-radius: 16px; }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="card">
        <div class="logo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none">
                <rect width="48" height="48" rx="12" fill="url(#a)"/>
                <path d="M24 12c-2.5 0-4.5 1-6 2.5l-1.5-1.5C18.5 11 21 10 24 10s5.5 1 7.5 3L30 14.5C28.5 13 26.5 12 24 12zm8 6l-2 2c1.5 1.5 2.5 3.5 2.5 6H36c0-3.5-1.5-6.5-4-8zm-16 0c-2.5 1.5-4 4.5-4 8h3.5c0-2.5 1-4.5 2.5-6l-2-2zm3 5a5 5 0 1 0 10 0 5 5 0 0 0-10 0zm-5 7a3 3 0 1 0 0 6h20a3 3 0 1 0 0-6H14z" fill="#fff"/>
                <defs><linearGradient id="a" x1="0" y1="0" x2="48" y2="48"><stop stop-color="#6366f1"/><stop offset="1" stop-color="#7c3aed"/></linearGradient></defs>
            </svg>
        </div>
        <h1>Masuk ke Panel Admin</h1>

        @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="field">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="checkbox-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat saya</label>
            </div>
            <button type="submit">Masuk</button>
        </form>
    </div>
</body>
</html>
