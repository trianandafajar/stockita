<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - StocKita</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-gray-800">

    {{-- <header class="w-full border-b bg-white sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">

            <a href="/" class="font-bold text-base sm:text-lg text-emerald-600">
                StocKita
            </a>

            <nav
                class="w-full sm:w-auto flex flex-wrap sm:flex-nowrap items-center gap-4 sm:gap-6 text-xs sm:text-sm justify-start sm:justify-end">
                <a href="/privacy" class="text-gray-600 hover:text-emerald-600">Privacy</a>
                <a href="/dmca" class="text-gray-600 hover:text-emerald-600">DMCA & Hak Cipta</a>
                <a href="/terms" class="text-gray-600 hover:text-emerald-600">Syarat & Ketentuan</a>
            </nav>

        </div>
    </header> --}}
    <x-landingpage-header />

    <main class="pt-20">
        {{ $slot }}
    </main>

    <x-footer />

</body>

</html>