<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} · Prayaag International School</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background-color: #f8fafc; color: #0f172a; }
        .card { max-width: 480px; padding: 2.5rem; background: #ffffff; border-radius: 1rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); text-align: center; border: 1px solid #e2e8f0; }
        .icon { width: 64px; height: 64px; margin: 0 auto 1.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; }
        .icon.success { background: #dcfce7; color: #15803d; }
        .icon.error { background: #fee2e2; color: #b91c1c; }
        h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.75rem; }
        p { color: #64748b; line-height: 1.5; margin-bottom: 1.5rem; }
        .btn { display: inline-block; padding: 0.75rem 1.5rem; background: #4f46e5; color: #fff; text-decoration: none; border-radius: 0.5rem; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon {{ $success ? 'success' : 'error' }}">
            {{ $success ? '✓' : '✕' }}
        </div>
        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>
        <a href="/" class="btn">Return to Home</a>
    </div>
</body>
</html>
