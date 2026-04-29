<x-app-layout title="Manajemen Transaksi & Stok">
    <script>
        const canCreateTransactions = @json(auth()->user()->can('create transactions'));
        const canDeleteTransactions = @json(auth()->user()->can('delete transactions'));
    </script>

    @if ($message = session('success') ?? (session('error') ?? (session('warning') ?? session('info'))))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
                let type =
                    "{{ session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : 'info')) }}";

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: "{{ $message }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
    </script>
    @endif

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                    Manajemen Transaksi
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">
                    Kelola semua transaksi anda
                </p>
            </div>

            <div class="flex gap-2">
                @can('create transactions')
                <a href="/transactions/create"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 text-white font-medium text-sm rounded-xl bg-green-500 hover:bg-green-600">
                    + Transaksi Baru
                </a>
                @endcan
            </div>
        </div>

        <form method="GET" action="{{ route('transactions.index') }}">
            <div class="bg-white p-4 rounded-xl shadow-sm border">
                <div class="flex flex-col sm:flex-row gap-3">

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari invoice, customer, atau produk..."
                        class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">

                    <div class="relative w-full sm:w-48">
                        <select name="status"
                            class="w-full appearance-none px-4 py-2 pr-10 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                            <option value="paid" {{ request('status')=='paid' ? 'selected' : '' }}>Paid</option>
                            <option value="unpaid" {{ request('status')=='unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>

                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>

                    <a href="{{ route('transactions.export', request()->query()) }}"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>

                        Export Excel
                    </a>

                </div>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-6 rounded-xl shadow-sm border text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">Total Transaksi</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border text-center">
                <div class="text-2xl font-bold text-green-600">Rp {{ number_format($stats['total_amount'] ?? 0) }}
                </div>
                <div class="text-sm text-gray-500">Total Nilai</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['pending'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">Belum Bayar</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border text-center">
                <div class="text-2xl font-bold text-gray-600">{{ $stats['items'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">Total Item</div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($transactions as $trx)
            <div class="bg-white p-6 rounded-xl shadow-sm border hover:shadow-md transition-all group">

                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="font-bold text-xl text-gray-900">
                            {{ $trx->invoice_code }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $trx->created_at->format('d M Y H:i') }}
                        </p>
                    </div>

                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $trx->type == 'in' ? 'bg-green-100 text-green-500' : 'bg-red-100 text-red-800' }}">
                        {{ $trx->type == 'in' ? 'MASUK' : 'KELUAR' }}
                    </span>
                </div>

                <div class="flex justify-between items-center mb-4">
                    <span
                        class="text-xs px-3 py-1 rounded-full font-medium
                        {{ $trx->status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ strtoupper($trx->status) }}
                    </span>

                    <span class="text-sm font-semibold text-gray-700">
                        {{ $trx->items->sum('qty') ?? 0 }} item
                    </span>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-1 text-sm">
                        <span class="text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </span>
                        {{ $trx->customer->user->name ?? 'Customer Umum' }}
                    </div>

                    @if ($trx->items->count() > 0)
                    <div class="text-xs text-gray-500">
                        Produk: {{ $trx->items->pluck('product.name')->take(2)->implode(', ') }}
                        @if ($trx->items->count() > 2)
                        +{{ $trx->items->count() - 2 }} lainnya
                        @endif
                    </div>
                    @endif
                </div>

                <div class="border-t pt-4 mb-4">
                    <p class="text-2xl font-bold text-gray-900">
                        Rp {{ number_format($trx->total, 0, ',', '.') }}
                    </p>
                </div>

                <div x-data class="flex flex-wrap gap-2">
                    @if ($trx->status != 'paid')
                    <button onclick="confirmPayment({{ $trx->id }})"
                        class="flex-1 px-3 py-2 text-xs bg-green-100 text-green-700 rounded-lg hover:bg-green-200 font-medium">
                        Bayar
                    </button>
                    @endif

                    {{-- <a href="/transactions/{{ $trx->id }}/edit"
                        class="px-3 py-2 text-xs bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200">
                        Edit
                    </a> --}}
                    @can('view transactions')
                    <a href="/transactions/{{ $trx->id }}"
                        class="px-3 py-2 text-xs bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200">
                        Detail
                    </a>
                    @endcan

                    @can('delete transactions')
                    <button @click="if (!canDeleteTransactions) {
                                Swal.fire({
                                    toast: true,
                                    icon: 'error',
                                    position: 'top-end',
                                    title: 'Kamu tidak punya izin menghapus produk!',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            } else {
                                $dispatch('open-modal', { name: 'delete-transaksi', id: {{ $trx->id }} })
                            }"
                        class="px-3 py-2 text-xs bg-red-100 text-red-700 rounded-lg {{ auth()->user()->can('delete transactions') ? 'hover:bg-red-200' : 'cursor-not-allowed opacity-50' }}">
                        Hapus
                    </button>
                    @endcan
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-20 text-gray-400">
                <div class="mb-6 flex justify-center">
                    <div class="p-6">
                        <svg class="w-24 h-24 mx-auto text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-gray-500">Belum ada transaksi</h3>
            </div>
            @endforelse
        </div>

        <div>
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    </div>

    {{-- delete modal --}}
    <x-modal name="delete-transaksi" maxWidth="md">
        <div x-data="{ transaksiId: null }" x-on:open-modal.window="
            if ($event.detail.name === 'delete-transaksi') {
                transaksiId = $event.detail.id
            }" class="p-6">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    Hapus Transaksi
                </h3>

                <button type="button" @click="$dispatch('close-modal', 'delete-transaksi')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="text-center space-y-3">
                <p class="text-gray-700 text-md">
                    Apakah kamu yakin ingin menghapus transaksi ini?
                </p>

                <p class="text-sm text-gray-400">
                    Data yang dihapus tidak dapat dikembalikan.
                </p>
            </div>

            <form :action="`/transactions/${transaksiId}`" method="POST" class="mt-6">
                @csrf
                @method('DELETE')

                <div class="flex gap-3">
                    <button type="button" @click="$dispatch('close-modal', 'delete-transaksi')"
                        class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>

                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium shadow-sm hover:shadow transition">
                        Ya, Hapus
                    </button>
                </div>
            </form>

        </div>
    </x-modal>
</x-app-layout>