{{-- Standalone on purpose: shown when the database or cache may be down. --}}
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>{{ __('site.server_error_title') }}</title>
<style>
  body { margin: 0; min-height: 100vh; display: grid; place-items: center; font-family: Cairo, system-ui, sans-serif;
         background: linear-gradient(180deg, #0C7F89 0%, #063135 100%); color: #fff; text-align: center; }
  main { padding: 24px; } h1 { font-size: 28px; margin: 0 0 8px; } p { opacity: .85; }
  a { display: inline-block; margin-top: 12px; padding: 10px 22px; border-radius: 999px; background: #A88344; color: #fff; text-decoration: none; }
</style>
</head>
<body><main><h1>{{ __('site.server_error_title') }}</h1><p>{{ __('site.server_error_text') }}</p><a href="/">{{ __('site.back_home') }}</a></main></body>
</html>
