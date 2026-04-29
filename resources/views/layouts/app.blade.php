@props(['title' => 'Dashboard'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- font --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<script>
    const VAPID_PUBLIC_KEY = "{{ config('webpush.vapid.public_key') }}";

if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.register('/js/sw.js').then(registration => {

        registration.pushManager.getSubscription().then(existing => {
            if (existing) return;

            // Auto subscribe saat halaman dibuka
            Notification.requestPermission().then(permission => {
                if (permission !== 'granted') return;

                registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
                }).then(subscription => {
                    const key = subscription.getKey('p256dh');
                    const token = subscription.getKey('auth');

                    fetch('/push/subscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            endpoint: subscription.endpoint,
                            publicKey: key ? btoa(String.fromCharCode(...new Uint8Array(key))) : null,
                            authToken: token ? btoa(String.fromCharCode(...new Uint8Array(token))) : null,
                            contentEncoding: (PushManager.supportedContentEncodings || ['aesgcm'])[0]
                        })
                    });
                });
            });
        });

    });
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = atob(base64);
    return Uint8Array.from([...rawData].map(c => c.charCodeAt(0)));
}
</script>

<body class="font-figtree bg-gray-50 antialiased">

    <div class="flex w-full min-h-screen overflow-hidden">

        {{-- sidebar --}}
        <x-sidebar />

        {{-- main content --}}
        <div id="mainContent" class="flex flex-col flex-1 min-w-0">

            {{-- header --}}
            <x-header :title="$title" />

            {{-- content --}}
            <main class="flex-1 px-3 p-6 pt-20 lg:p-8 overflow-y-auto w-full lg:pt-20">
                <div class=mx-auto">
                    {{ $slot }}
                </div>

                {{-- modal logout --}}
                <x-modal name="logout" maxWidth="md">
                    <div class="p-6">

                        <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 flex items-center justify-center rounded-full bg-red-100 text-red-600">

                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                                    </svg>
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900">
                                    Logout Akun
                                </h3>
                            </div>

                            <button type="button" @click="$dispatch('close-modal', 'logout')"
                                class="text-gray-400 hover:text-gray-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                            Apakah kamu yakin ingin keluar dari akun? Kamu perlu login kembali untuk mengakses aplikasi.
                        </p>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <div class="flex gap-3">
                                <button type="button" @click="$dispatch('close-modal', 'logout')"
                                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                    Batal
                                </button>

                                <button type="submit"
                                    class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium shadow-sm hover:shadow transition">
                                    Ya, Logout
                                </button>
                            </div>
                        </form>
                    </div>
                </x-modal>

            </main>
        </div>
    </div>
</body>

</html>