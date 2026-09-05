<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquavend — Smart Water Vending Management</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 3rem;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .logo span { color: #bae6fd; }

        nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 2rem;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        nav a:hover { opacity: 1; }

        .hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            max-width: 800px;
            line-height: 1.15;
            margin-bottom: 1.25rem;
        }

        .hero p {
            font-size: 1.15rem;
            max-width: 560px;
            opacity: 0.9;
            margin-bottom: 2.5rem;
        }

        .cta-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            padding: 0.9rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: transform 0.15s ease, opacity 0.15s ease;
        }

        .btn:hover { transform: translateY(-2px); }

        .btn-primary {
            background: #fff;
            color: #0369a1;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            max-width: 900px;
            margin-top: 4rem;
            width: 100%;
        }

        .feature {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: left;
        }

        .feature h3 {
            font-size: 1.05rem;
            margin-bottom: 0.5rem;
        }

        .feature p {
            font-size: 0.9rem;
            opacity: 0.85;
            margin: 0;
            max-width: none;
        }

        footer {
            text-align: center;
            padding: 1.5rem;
            font-size: 0.85rem;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">Aqua<span>vend</span></div>
        <div>
            <a href="#features">Features</a>
            <a href="/api/stations">API</a>
        </div>
    </nav>

    <div class="hero">
        <h1>Run your water vending business on autopilot</h1>
        <p>
            Track stations, manage stock, process sales, and serve customers —
            all from one platform built for water vending operators.
        </p>

        <div class="cta-group">
            <a href="http://localhost:5173" class="btn btn-primary">Open App</a>
            <a href="/api/stations" class="btn btn-secondary">View API</a>
        </div>

        <div class="features" id="features">
            <div class="feature">
                <h3>💧 Station Monitoring</h3>
                <p>Live stock levels across every vending point, refilled and tracked in real time.</p>
            </div>
            <div class="feature">
                <h3>🧾 Sales & Transactions</h3>
                <p>Every sale recorded instantly, with automatic stock deduction per station.</p>
            </div>
            <div class="feature">
                <h3>📶 Works Offline</h3>
                <p>Field agents can record sales without signal — synced automatically once back online.</p>
            </div>
        </div>
    </div>

    <footer>
        &copy; {{ date('Y') }} Aquavend. Built with Laravel.
    </footer>
</body>
</html>