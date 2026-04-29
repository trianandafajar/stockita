<x-app-layout title="Detail Transaksi #{{ $transaction->invoice_code }}">

    <div class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-green-500 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Daftar Item ({{ $transaction->items->count() }})
                    </h2>

                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($transaction->items as $item)
                            <div
                                class="group flex items-center gap-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all border hover:shadow-sm hover:border-gray-200">
                                <div
                                    class="w-16 h-16 rounded-xl overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 flex-shrink-0">
                                    @if ($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}"
                                            class="w-full h-full object-cover" alt="{{ $item->product->name }}"
                                            onerror="this.parentElement.innerHTML='<svg class=\"w-8 h-8 mx-auto mt-4 text-gray-400\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\"></path></svg>'">
                                    @else
                                        <div
                                            class="w-full h-full bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 truncate">{{ $item->product->name }}</h4>
                                    <p class="text-sm text-gray-500">Rp {{ number_format($item->price, 0, ',', '.') }}
                                        x {{ $item->qty }}</p>
                                </div>

                                <div class="text-right">
                                    <p class="font-bold text-lg text-gray-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-lg font-medium">Tidak ada item</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-2xl shadow-sm border border-green-500 p-6 sticky">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                        Ringkasan Pembayaran
                    </h2>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Subtotal</span>
                            <span class="font-semibold">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                        </div>

                        @if ($transaction->paid)
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-sm text-gray-600">Dibayar</span>
                                <span class="font-semibold text-green-600">Rp
                                    {{ number_format($transaction->paid, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        @if ($transaction->change)
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-sm text-gray-600">Kembali</span>
                                <span class="font-semibold text-green-600">Rp
                                    {{ number_format($transaction->change, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div
                        class="bg-gradient-to-r from-emerald-50 to-green-50 border border-green-100 rounded-xl p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-700 font-medium">Status Pembayaran</p>
                                <p class="text-2xl font-bold text-green-600">
                                    {{ $transaction->status == 'paid' ? 'LUNAS' : 'MENUNGGU' }}
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($transaction->receipt)
                    <div class="bg-white rounded-2xl shadow-sm border border-green-500 p-6 mt-6">
                        <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Pratinjau Struk
                        </h3>
                        <div class="border rounded-xl overflow-hidden shadow-inner max-h-64 overflow-y-auto">
                            <img src="{{ asset('storage/' . $transaction->receipt) }}" class="w-full h-auto"
                                alt="Struk Transaksi {{ $transaction->invoice_code }}">
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function markAsPaid(id) {
            if (!confirm('Tandai transaksi ini sebagai lunas?')) return;

            fetch(`/transactions/${id}/pay`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => alert('Terjadi kesalahan'));
        }

        function printReceipt(id) {
            window.open(`/transactions/${id}/receipt`, '_blank');
        }
    </script>

    <style>
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 20px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 20px;
            border: 3px solid #f1f1f1;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

</x-app-layout>
