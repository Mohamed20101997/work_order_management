{{-- PWA meta --}}
<link rel="manifest" href="/manifest.json?v={{ filemtime(public_path('manifest.json')) }}">
<meta name="theme-color" content="#0f172a">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Assets">
<link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-192.png">
<link rel="stylesheet" href="/css/admin.css?v={{ filemtime(public_path('css/admin.css')) }}">
