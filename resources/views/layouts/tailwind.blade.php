<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Staff Management') — Aquavend</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <nav class="bg-sky-800 px-8 py-4 flex justify-between items-center">
        <div class="text-white font-bold text-lg">Aqua<span class="text-sky-300">vend</span> — Staff</div>
        <div class="space-x-6">
            <a href="/dashboard" class="text-sky-200 hover:text-white text-sm">Dashboard</a>
            <a href="/staff" class="text-sky-200 hover:text-white text-sm">Staff</a>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto py-8 px-4">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-3 rounded-lg mb-4 text-sm">{{ $errors->first() }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>