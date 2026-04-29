<x-app-layout title="Manajemen Transaksi & Stok">
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
                <a href="/admin/transactions/create"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 text-white font-medium text-sm rounded-xl bg-green-500 hover:bg-green-600">
                    + Transaksi Baru
                </a>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-6 rounded-xl shadow-sm border text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">Total Transaksi</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border text-center">
                <div class="text-2xl font-bold text-green-600">Rp {{ number_format($stats['total_amount'] ?? 0) }}</div>
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

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <form method="GET" action="{{ route('admin.transactions.index') }}" class="p-6">
                <div class="flex flex-col lg:flex-row gap-3"> <input type="text" name="search"
                        value="{{ request('search') }}" placeholder="Cari invoice, customer, atau produk..."
                        class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">

                    <div class="relative w-full sm:w-48">
                        <select name="store"
                            class="w-full appearance-none px-4 py-2 pr-10 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Toko</option>
                            @foreach ($stores as $store)
                            <option value="{{ $store->id }}" {{ request('store')==$store->id ? 'selected' : '' }}>
                                {{ $store->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative w-full sm:w-48">
                        <select name="status"
                            class="w-full appearance-none px-4 py-2 pr-10 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Status</option>
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

                        Export
                    </a>

                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Invoice</th>
                            <th class="px-6 py-3">Toko</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Customer</th>
                            <th class="px-6 py-3">Produk</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse ($transactions as $trx)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                {{ $trx->invoice_code }}
                                <p class="text-xs text-gray-400">
                                    {{ $trx->type == 'in' ? 'MASUK' : 'KELUAR' }}
                                </p>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $trx->store->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                {{ $trx->created_at->format('d M Y H:i') }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $trx->customer->user->name ?? 'Customer Umum' }}
                            </td>

                            <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">
                                {{ $trx->items->pluck('product.name')->take(2)->implode(', ') }}
                                @if ($trx->items->count() > 2)
                                +{{ $trx->items->count() - 2 }} lainnya
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 text-xs rounded-full
                            {{ $trx->status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ strtoupper($trx->status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap   font-semibold text-gray-800 whitespace-nowrap">
                                Rp {{ number_format($trx->total, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div x-data class="flex justify-end gap-2">

                                    @if ($trx->status != 'paid')
                                    <button onclick="confirmPayment({{ $trx->id }})"
                                        class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
                                        Bayar
                                    </button>
                                    @endif

                                    <a href="/admin/transactions/{{ $trx->id }}"
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200">
                                        Detail
                                    </a>

                                    @can('delete transactions')
                                    <button
                                        @click="$dispatch('open-modal', { name: 'delete-transaksi', id: {{ $trx->id }} })"
                                        class="px-3 py-1 text-xs bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                                        Hapus
                                    </button>
                                    @endcan
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400">
                                Belum ada transaksi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t">
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

            <form :action="`/admin/transactions/${transaksiId}`" method="POST" class="mt-6">
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