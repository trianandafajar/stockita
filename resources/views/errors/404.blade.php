<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 to-primary-50 antialiased text-slate-900" style="font-family: 'Inter', sans-serif;">
    <div class="relative isolate flex min-h-screen items-center justify-center overflow-hidden px-4 py-10 sm:px-6 lg:px-8">
        <div class="w-full max-w-4xl">
            <div class="text-center">
                <img src="/image/icon/icon.png" alt="StocKita Logo" class="mx-auto h-20 w-20 sm:h-24 sm:w-24">
                <p class="mt-4 text-xs font-semibold uppercase tracking-[0.35em] text-primary-600">{{ config('app.name', 'StocKita') }}</p>
            </div>

            <div class="text-center">
                <h1 class="mt-3 bg-gradient-to-r from-primary-600 to-teal-600 bg-clip-text text-[8rem] font-extrabold leading-none text-transparent sm:text-3xl lg:text-7xl">404</h1>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900 sm:text-4xl">Page not found</h2>
                <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-slate-600 sm:text-xl">
                    The page you are looking for may have been moved, deleted, or never existed.
                    Let&apos;s get you back on track.
                </p>
            </div>

            <div class="mx-auto mt-10 flex w-full max-w-xl flex-col gap-3 sm:flex-row">
                <a href="{{ url('/') }}"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-primary-500 px-5 py-3 text-sm font-semibold text-white transition-all hover:-translate-y-0.5 hover:bg-primary-600">
                    Go to Home
                </a>
                <a href="javascript:history.back()"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-amber-500 px-5 py-3 text-sm font-semibold text-white transition-all hover:-translate-y-0.5 hover:bg-amber-600">
                    Go Back
                </a>
            </div>
        </div>
    </div>
</body>

</html>
