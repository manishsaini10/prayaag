<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $success ? 'Instagram Connected' : 'Connection Error' }}</title>
</head>
<body style="font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#0f172a;color:#f8fafc">
    <div style="background:#1e293b;border:1px solid #334155;border-radius:16px;padding:32px 24px;text-align:center;max-width:360px;width:90%;box-shadow:0 20px 25px -5px rgba(0,0,0,0.5)">
        @if ($success)
            <div style="width:56px;height:56px;border-radius:50%;background:rgba(34,197,94,0.15);color:#22c55e;display:grid;place-items:center;margin:0 auto 16px;font-size:28px">✓</div>
            <h2 style="margin:0 0 8px;font-size:18px">Connected Successfully!</h2>
            <p style="margin:0 0 16px;font-size:13.5px;color:#94a3b8">Account <strong>@{{ $username }}</strong> is now linked. Closing window...</p>
        @else
            <div style="width:56px;height:56px;border-radius:50%;background:rgba(239,68,68,0.15);color:#ef4444;display:grid;place-items:center;margin:0 auto 16px;font-size:28px">✕</div>
            <h2 style="margin:0 0 8px;font-size:18px">Connection Failed</h2>
            <p style="margin:0 0 16px;font-size:13.5px;color:#94a3b8">{{ $errorMessage }}</p>
            <button style="padding:10px 20px;border-radius:8px;background:#475569;color:#fff;border:none;cursor:pointer" onclick="window.close()">Close Window</button>
        @endif
    </div>

    <script>
        if (window.opener) {
            @if ($success)
                try {
                    window.opener.postMessage({ type: 'IG_OAUTH_SUCCESS', username: '{{ $username }}' }, '*');
                } catch(e){}
                setTimeout(function() {
                    window.opener.location.reload();
                    window.close();
                }, 1000);
            @else
                setTimeout(function() {
                    window.close();
                }, 4000);
            @endif
        } else {
            @if ($success)
                setTimeout(function() {
                    window.location.href = "{{ route('admin.instagram.dashboard') }}";
                }, 1500);
            @endif
        }
    </script>
</body>
</html>
