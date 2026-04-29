<x-app-layout title="Detail Pesanan">
    <div class="py-6 space-y-6 mx-auto">
        <a href="{{ route('buyer.orders') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 hover:underline transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>

        <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100">
            <div class="flex justify-between items-start md:items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">
                        {{ $order->invoice_code }}
                    </h1>
                    <p class="text-sm text-gray-500">
                        {{ $order->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <span
                    class="px-4 py-2 rounded-full text-sm font-semibold border whitespace-nowrap
                    {{ $order->status == 'paid' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200' }}">
                    {{ strtoupper($order->status) }}
                </span>
            </div>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Item Pesanan</h2>

            <div class="space-y-4">
                @forelse ($order->items as $item)
                    <div class="flex items-center justify-between py-4 border-b border-gray-100 last:border-b-0">
                        <div class="flex items-start gap-4 flex-1">
                            <div class="w-20 h-20 bg-gray-100 rounded-xl flex-shrink-0 overflow-hidden">
                                @if ($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                        alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                        <span class="text-xs text-gray-500">No Image</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 mb-1 leading-tight">
                                    {{ Str::limit($item->product->name, 50) }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-2">
                                    {{ $item->qty }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-900">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500">
                        <p>Tidak ada item dalam pesanan ini</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-gradient-to-r from-emerald-50 to-green-50 p-8 rounded-3xl shadow-lg border border-emerald-100">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 text-emerald-600">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>

                Ringkasan Pembayaran
            </h2>

            <div class="bg-white/50 backdrop-blur-sm p-6 rounded-2xl border border-white/50 space-y-4">
                <div class="flex justify-between py-3 border-b border-emerald-100">
                    <span class="text-gray-700 font-medium">Total Belanja</span>
                    <span class="text-lg font-bold text-gray-900">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between py-3 border-b border-emerald-100">
                    <span class="text-gray-700 font-medium">Dibayar</span>
                    <span class="text-lg font-bold text-emerald-600">
                        Rp {{ number_format($order->paid, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between items-end py-4 pt-6">
                    <div>
                        <span class="text-sm text-gray-600 block mb-1">Kembali</span>
                        @if ($order->change > 0)
                            <span class="text-2xl font-black text-emerald-600">
                                Rp {{ number_format($order->change, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-2xl font-black text-gray-400">Rp 0</span>
                        @endif
                    </div>

                    @if ($order->receipt)
                        <div class="flex flex-col items-end gap-2">
                            <span class="text-xs text-gray-500 text-right">Bukti Pembayaran</span>
                            <div class="w-24 h-24 bg-white rounded-xl shadow-sm overflow-hidden border">
                                <img src="{{ asset('storage/' . $order->receipt) }}" alt="Bukti Pembayaran"
                                    class="w-full h-full object-cover hover:scale-110 transition-transform duration-200">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
