<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-24 w-24 flex items-center justify-center bg-red-100 rounded-full mb-6">
                <svg class="h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">403</h1>
            <p class="text-lg text-gray-600 mb-6">Forbidden</p>
        </div>

        <div class="text-center">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Akses Ditolak</h2>
            <p class="text-gray-500 mb-8 text-lg">
                Anda tidak memiliki izin untuk mengakses halaman ini.
            </p>
        </div>

        <div>
            <a href="javascript:history.back()"
                class="block w-full max-w-sm mx-auto flex justify-center py-3 px-6 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                Kembali ke Halaman Sebelumnya
            </a>
        </div>
    </div>
</body>

</html>
