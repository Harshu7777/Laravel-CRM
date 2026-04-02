<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #0b0e13;
            --surface: #111620;
            --accent: #3b82f6;
        }
        body {
            background: var(--bg);
            color: #e2e8f0;
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: var(--surface);
            border: 1px solid #1e2535;
            border-radius: 20px;
            padding: 40px;
            max-width: 440px;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="text-center mb-4">
        <h4 class="fw-bold">Forgot Password</h4>
        <p class="text-muted">Enter your email and we'll send you a reset link.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('reset-password') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            @error('email')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 py-3">
            Send Password Reset Link
        </button>
    </form>

    <div class="text-center mt-4">
        <a href="{{ route('login') }}" class="text-muted">← Back to Login</a>
    </div>
</div>
</body>
</html>