<x-app-layout title="Dashboard Buyer">

    <div class="py-6 space-y-6">


        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-6 rounded-3xl text-white shadow-lg">
            <h1 class="text-2xl font-bold">
                Halo, {{ auth()->user()->name }} 👋
            </h1>
            <p class="opacity-90 mt-1">
                Selamat datang kembali, cek pesananmu di sini 🚀
            </p>
        </div>


        <div class="grid md:grid-cols-3 gap-6">


            <div class="bg-white p-6 rounded-2xl shadow border">
                <p class="text-sm text-gray-500">Total Pesanan</p>
                <p class="text-2xl font-bold mt-1">
                    {{ $totalOrders }}
                </p>
            </div>


            <div class="bg-white p-6 rounded-2xl shadow border">
                <p class="text-sm text-gray-500">Total Belanja</p>
                <p class="text-2xl font-bold mt-1 text-emerald-600">
                    Rp {{ number_format($totalSpent, 0, ',', '.') }}
                </p>
            </div>


            <div class="bg-white p-6 rounded-2xl shadow border">
                <p class="text-sm text-gray-500">Pesanan Terakhir</p>
                <p class="text-sm mt-1">
                    {{ $lastOrder?->created_at?->format('d M Y') ?? '-' }}
                </p>
            </div>

        </div>


        <div class="bg-white p-6 rounded-3xl shadow border">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Pesanan Terbaru</h2>

                <a href="{{ route('buyer.orders') }}" class="text-sm text-emerald-600 font-medium hover:underline">
                    Lihat Semua
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($recentOrders as $order)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <p class="font-medium">{{ $order->invoice_code }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $order->created_at->format('d M Y H:i') }}
                            </p>
                        </div>

                        <div class="text-right">
                            <p class="font-semibold text-emerald-600">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </p>

                            <a href="{{ route('buyer.orders.show', $order->id) }}"
                                class="text-xs text-blue-500 underline">
                                Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">
                        Belum ada pesanan
                    </p>
                @endforelse
            </div>
        </div>
    </div>

</x-app-layout>
