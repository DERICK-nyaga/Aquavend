<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Aquavend')</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, sans-serif; background: #f0f9ff; margin: 0; color: #1e293b; }
        nav { background: #0369a1; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        nav .brand { color: #fff; font-weight: 700; font-size: 1.1rem; }
        nav .links a { color: #bae6fd; text-decoration: none; margin-left: 1.5rem; font-size: 0.9rem; }
        nav .links a:hover { color: #fff; }
        .container { max-width: 1000px; margin: 2rem auto; padding: 0 1.5rem; }
        h1 { font-size: 1.5rem; margin-bottom: 1rem; color: #0369a1; }
        .card { background: #fff; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 0.7rem; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; }
        th { color: #64748b; font-weight: 600; }
        .btn { display: inline-block; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; border: none; cursor: pointer; }
        .btn-primary { background: #0ea5e9; color: #fff; }
        .btn-edit { background: #e0f2fe; color: #0369a1; }
        .btn-delete { background: #fee2e2; color: #dc2626; }
        .top-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
        form.inline { display: inline; }
        label { display: block; font-size: 0.85rem; margin-bottom: 0.3rem; color: #333; }
        input, select { width: 100%; padding: 0.6rem; margin-bottom: 1rem; border: 1px solid #cbd5e1; border-radius: 6px; }
        .error { color: #dc2626; font-size: 0.85rem; margin-bottom: 1rem; }
        .badge { padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        .badge-maintenance { background: #fef9c3; color: #854d0e; }
    </style>
</head>
<body>
    <nav>
        <div class="brand">Aqua<span style="color:#7dd3fc">vend</span></div>
        <div class="links">
            <a href="/dashboard">Dashboard</a>
            <a href="/stations">Stations</a>
            <a href="/products">Products</a>
            <a href="/customers">Customers</a>
            <a href="/transactions">Transactions</a>
            <a href="/stock-refills">Refills</a>
            <a href="/suppliers">Suppliers</a>
            <a href="/expenses">Expenses</a>
            <a href="/reports">Reports</a>
            <a href="/stock-transfers">Transfers</a>
            <a href="/notifications">Notifications</a>
            <form class="inline" method="POST" action="/logout" style="display:inline">
                @csrf
                <button class="btn" style="background:none;color:#fecaca;cursor:pointer;">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        @if (session('success'))
            <div class="card" style="background:#dcfce7;color:#166534;margin-bottom:1rem;">{{ session('success') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>