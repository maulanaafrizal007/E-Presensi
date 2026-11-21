<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') - SIKAP DPD RI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: -apple-system, 'Segoe UI', sans-serif; background: #f4f6fb; }
        .error-card {
            max-width: 480px; margin: 10vh auto; background: white;
            border-radius: 16px; padding: 3rem 2rem; text-align: center;
            border: 1px solid #e2e8f0;
        }
        .error-code { font-size: 5rem; font-weight: 800; color: #1a3561; line-height: 1; }
    </style>
</head>
<body>
    <div class="error-card shadow-sm">
        <div class="error-code">@yield('code')</div>
        <h4 class="mt-3 mb-2">@yield('title')</h4>
        <p class="text-muted">@yield('message')</p>
        <a href="{{ url('/') }}" class="btn btn-primary mt-2">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
