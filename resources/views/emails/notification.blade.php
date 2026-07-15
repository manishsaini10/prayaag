<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;margin:0;padding:0;background:#f5f5f5">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px">
<tr><td align="center">
<table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.08)">
<tr><td style="padding:32px 32px 0">
<h1 style="font-size:22px;font-weight:700;color:#0e2f5e;margin:0 0 8px">{{ config('app.name') }}</h1>
<hr style="border:none;border-top:1px solid #e5e7eb;margin:16px 0">
</td></tr>
<tr><td style="padding:0 32px">
<p style="font-size:15px;line-height:1.6;color:#374151;margin:0 0 16px">{{ $body }}</p>
@if($actionUrl && $actionText)
<table cellpadding="0" cellspacing="0" style="margin:24px 0">
<tr><td style="background:#0b2545;border-radius:8px;padding:12px 28px">
<a href="{{ $actionUrl }}" style="color:#fff;text-decoration:none;font-size:14px;font-weight:600">{{ $actionText }}</a>
</td></tr>
</table>
@endif
</td></tr>
<tr><td style="padding:24px 32px 32px">
<hr style="border:none;border-top:1px solid #e5e7eb;margin:0 0 16px">
<p style="font-size:13px;color:#9ca3af;margin:0">Sent from {{ config('app.name') }} control center.</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
