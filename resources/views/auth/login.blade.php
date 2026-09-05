<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login — Aquavend</title>
    <style>
        body { font-family: sans-serif; background: #f0f9ff; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { background: #fff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); width: 100%; max-width: 380px; }
        h1 { font-size: 1.4rem; margin-bottom: 1.5rem; color: #0369a1; }
        label { display: block; font-size: 0.85rem; margin-bottom: 0.3rem; color: #333; }
        input { width: 100%; padding: 0.65rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 0.75rem; background: #0ea5e9; color: #fff; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
        .error { color: #dc2626; font-size: 0.85rem; margin-bottom: 1rem; }
        p { margin-top: 1rem; font-size: 0.9rem; text-align: center; }
        a { color: #0369a1; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Log in to Aquavend</h1>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Log In</button>
        </form>

        <p>Don't have an account? <a href="/register">Register</a></p>
    </div>
</body>
</html>