<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-figtree bg-gray-50 antialiased">
    <div class="bg-gray-50 py-16">
        <div class="max-w-5xl mx-auto px-6">

            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900">
                    Selesaikan Pembayaran
                </h1>
                <p class="text-gray-500 mt-2">
                    Satu langkah lagi untuk mengaktifkan paketmu
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">

                <div class="bg-white p-8 rounded-2xl shadow-sm border">
                    <h2 class="text-xl font-semibold mb-4">
                        {{ $plan->name }} Plan
                    </h2>

                    <div class="text-4xl font-bold text-emerald-600 mb-4">
                        <span id="priceDisplay">
                            Rp {{ $interval == 'yearly' ? $plan->yearly_price : $plan->price }}
                        </span>
                        <span class="text-base text-gray-500">
                            /{{ $interval == 'yearly' ? 'tahun' : 'bulan' }}
                        </span>
                    </div>

                    <ul class="space-y-2 text-gray mb-6">
                        @foreach ($plan->features as $feature)
                        <li>✔ {{ $feature }}</li>
                        @endforeach
                    </ul>

                    @if ($interval == 'yearly')
                    <div class="text-sm text-emerald-600 font-semibold">
                        Hemat lebih dengan paket tahunan
                    </div>
                    @endif
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border flex flex-col justify-between">

                    <div>
                        <h2 class="text-lg font-semibold mb-4">
                            Ringkasan Pembayaran
                        </h2>

                        <div class="flex justify-between mb-2 text-gray-600">
                            <span>Paket</span>
                            <span>{{ $plan->name }}</span>
                        </div>

                        <div class="flex justify-between mb-2 text-gray-600">
                            <span>Durasi</span>
                            <span>{{ $interval }}</span>
                        </div>

                        <div class="flex justify-between font-bold text-lg mt-4">
                            <span>Total</span>
                            <span>
                                Rp
                                {{ number_format($interval == 'yearly' ? $plan->yearly_price : $plan->price, 2, ',',
                                '.') }}
                            </span>
                        </div>
                    </div>

                    <button onclick="pay({{ $plan->id }}, '{{ $interval }}')"
                        class="mt-6 w-full bg-emerald-500 hover:bg-emerald-600 text-white py-3 rounded-xl font-semibold transition">
                        Bayar Sekarang
                    </button>
                    <div class="flex justify-center">
                        <p class="flex items-center gap-1 text-xs text-gray-400  mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            Pembayaran aman via Midtrans
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const priceEl = document.getElementById('priceDisplay');

            let rawPrice = {{ $interval == 'yearly' ? $plan->yearly_price : $plan->price }};

            priceEl.innerHTML = 'Rp ' + formatRupiahK(rawPrice);
        });

        function formatRupiahK(num) {
            if (num == 0) return 'Gratis';

            if (num >= 1000) {
                return (num / 1000) + 'K';
            }

            return num;
        }

        async function pay(plan_id, interval) {
            try {
                const res = await fetch(`/pay`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        plan_id,
                        interval
                    })
                });

                const data = await res.json();

                if (data.free) {
                    window.location.href = '/dashboard';
                    return;
                }

                snap.pay(data.snap_token, {
                    onSuccess: function() {
                        window.location.href = '/dashboard';
                    },
                    onPending: function() {
                        Swal.fire({
                            toast: true,
                            icon: 'warning',
                            position: 'top-end',
                            title: 'Menunggu pembayaran',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    },
                    onError: function() {
                        Swal.fire({
                            toast: true,
                            icon: 'error',
                            position: 'top-end',
                            title: 'Pembayaran gagal',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                });

            } catch (err) {
                console.error(err);
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    position: 'top-end',
                    title: 'Terjadi error pada system, tunggu beberapa saat untuk mencoba lagi',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        }
    </script>
</body>

</html>