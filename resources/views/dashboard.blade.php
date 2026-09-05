<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Aquavend</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; background: #f0f9ff; }
        h1 { color: #0369a1; }
        form { display: inline; }
        button { background: none; border: none; color: #dc2626; cursor: pointer; text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Welcome, {{ auth()->user()->name }} 👋</h1>
    <p>You're logged into Aquavend.</p>

    <form method="POST" action="/logout">
        @csrf
        <button type="submit">Log out</button>
    </form>
</body>
</html>