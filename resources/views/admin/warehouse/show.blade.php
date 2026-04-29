<x-app-layout title="Detail Gudang">
    <script>
        const canManageStockMovement = @json(auth()->user()->can('create inventory'));
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
        <div class="bg-white border border-slate-100 rounded-2xl p-4 sm:p-6 lg:p-8">

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <div class="flex items-start sm:items-center gap-3">

                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-green-500 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800">
                            {{ $warehouse->name }}
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500">
                            {{ $warehouse->location ?? 'Lokasi tidak tersedia' }}
                        </p>
                    </div>

                </div>

                <div x-data class="w-full sm:w-auto">
                    @can('create inventory')
                    <button @click="$dispatch('open-modal', {name:'create-stock'})" class="w-full sm:w-auto justify-center px-4 py-3 text-sm sm:text-base
                    rounded-xl flex items-center gap-2 text-white font-semibold
                  bg-green-500 hover:scale-[1.02] active:scale-95 transition">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>

                        Tambah Barang
                    </button>
                    @endcan
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-6 mt-6 pt-6 border-t">

                <div class="p-4 rounded-xl border text-center">
                    <div class="text-xl sm:text-2xl font-bold text-green-600">
                        {{ $stocks->count() }}
                    </div>
                    <div class="text-xs sm:text-sm text-slate-500">Total Produk</div>
                </div>

                <div class="p-4 rounded-xl border text-center">
                    <div class="text-xl sm:text-2xl font-bold text-blue-600">
                        {{ $stocks->sum('qty') }}
                    </div>
                    <div class="text-xs sm:text-sm text-slate-500">Total Stok</div>
                </div>

                <div class="p-4 rounded-xl border text-center">
                    <div class="text-lg sm:text-2xl font-bold text-emerald-600">
                        Rp {{ number_format($stocks->sum(fn($s) => $s->qty * $s->product->price), 0, ',', '.') }}
                    </div>
                    <div class="text-xs sm:text-sm text-slate-500">Nilai Stok</div>
                </div>

            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">

            @forelse ($stocks as $stock)
            <div class="bg-white border rounded-xl overflow-hidden group">

                <div class="relative aspect-[4/3] bg-gray-100">

                    <img src="{{ asset('storage/' . $stock->product->image) }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition">

                    <!-- QTY -->
                    <span class="absolute top-2 left-2 text-xs font-bold bg-green-500 text-white px-2 py-1 rounded-lg">
                        {{ $stock->qty }}
                    </span>

                    @canany(['adjust stock', 'delete inventory'])
                    <div x-data="{ open: false }" class="absolute top-2 right-2">

                        <button @click="open = !open"
                            class="w-6 h-6 flex items-center justify-center bg-black/40 text-white rounded-lg">
                            ⋮
                        </button>


                        <div x-show="open" @click.outside="open=false"
                            class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow border text-xs z-50">
                            @can('adjust stock')
                            <button
                                @click="$dispatch('open-modal', { name: 'add-stock', id: {{ $stock->id }} }); open=false"
                                class="w-full text-left px-3 py-2 flex gap-1 items-center hover:bg-green-50 text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="size-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Tambah
                            </button>

                            <button @click="$dispatch('open-modal', { 
                                        name: 'reduce-stock', 
                                        id: {{ $stock->id }}, 
                                        max: {{ $stock->qty }} 
                                    }); open=false"
                                class="w-full text-left px-3 py-2 flex gap-1 items-center hover:bg-yellow-50 text-yellow-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="size-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                </svg>
                                Kurangi
                            </button>
                            @endcan

                            @can('delete inventory')
                            <button
                                @click="$dispatch('open-modal', { name: 'delete-stock', id: {{ $stock->id }} }); open=false"
                                class="w-full text-left px-3 py-2 flex gap-1 items-center hover:bg-red-50 text-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="size-3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                Hapus
                            </button>
                            @endcan

                        </div>

                    </div>
                    @endcanany
                </div>

                <div class="p-3">

                    <h3 class="text-sm font-semibold truncate">
                        {{ $stock->product->name }}
                    </h3>

                    <p class="text-xs text-gray-500">
                        {{ $stock->product->sku }}
                    </p>

                    <p class="text-sm font-bold text-green-600 mt-1">
                        Rp {{ number_format($stock->product->price, 0, ',', '.') }}
                    </p>

                </div>

            </div>
            @empty
            <div class="col-span-full text-center text-gray-400 py-10">
                Belum ada produk di gudang ini
            </div>
            @endforelse

        </div>
    </div>

    {{-- create modal --}}
    <x-modal name="create-stock" maxWidth="md" :show="session('open_modal') === 'create-stock'">
        <div class="p-6">
            <form action="{{ route('admin.stocks.store') }}" method="POST">
                @csrf

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Tambah Barang</h3>
                    <button type="button"
                        @click="$el.closest('form').reset(); $dispatch('close-modal', 'create-stock')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium">Produk <span class="text-red-500">*</span></label>
                        <select name="product_id"
                            class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">--- Pilih Produk ---</option>
                            @foreach ($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->name }}
                            </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->createStock->get('product_id')"
                            class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div>
                        <label class="text-sm font-medium">Jumlah <span class="text-red-500">*</span></label>
                        <input type="number" name="qty"
                            class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <x-input-error :messages="$errors->createStock->get('qty')" class="mt-2 text-red-500 text-sm" />
                    </div>
                    <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}">

                    <div class="flex justify-end gap-2 pt-3">
                        <button @click="$el.closest('form').reset(); $dispatch('close-modal', 'create-stock')"
                            type="button" id="closeModal" class="px-3 py-2 text-sm bg-gray-200 rounded-lg">
                            Batal
                        </button>

                        <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- add stock --}}
    <x-modal name="add-stock" maxWidth="md" :show="session('open_modal') === 'add-stock'">
        <div x-data="{ stockId: null }" x-on:open-modal.window="
            if ($event.detail.name === 'add-stock') {
                stockId = $event.detail.id
            }" class="p-6">

            <form :action="`/admin/stocks/${stockId}`" method="POST">
                @csrf
                @method('PUT')

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Tambah Stok</h3>
                    <button type="button" @click="$el.closest('form').reset(); $dispatch('close-modal', 'add-stock')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium">Masukan Stok <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="qty" value="{{ old('qty') }}" placeholder="Masukan Angka"
                            class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <x-input-error :messages="$errors->addStock->get('qty')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="flex justify-end gap-2 pt-3">
                        <button @click="$el.closest('form').reset(); $dispatch('close-modal', 'add-stock')"
                            type="button" id="closeModal" class="px-3 py-2 text-sm bg-gray-200 rounded-lg">
                            Batal
                        </button>

                        <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- kurang modal --}}
    <x-modal name="reduce-stock" maxWidth="md">
        <div x-data="{
            stockId: null,
            qty: 1,
            max: 0,
        
            init() {
                this.$watch('qty', value => {
                    if (value > this.max) this.qty = this.max
                    if (value < 1) this.qty = 1
                })
            }
        }" x-on:open-modal.window="
        if ($event.detail.name === 'reduce-stock') {
            stockId = $event.detail.id
            max = $event.detail.max ?? 0

            qty = max
        }" class="p-6">

            <form :action="`/admin/stocks/${stockId}/reduce`" method="POST">
                @csrf
                @method('PUT')

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-yellow-600">Kurangi Stok</h3>

                    <button type="button" @click="$dispatch('close-modal', 'reduce-stock')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex items-center gap-3">

                    <button type="button" @click="if(qty > 1) qty--"
                        class="w-10 h-10 flex items-center justify-center bg-yellow-100 text-yellow-700 rounded-xl hover:bg-yellow-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                        </svg>
                    </button>

                    <input type="number" name="qty" x-model="qty" min="1" :max="max"
                        class="w-full h-10 text-center border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-400 focus:outline-none">

                    <button type="button" @click="if(qty < max) qty++"
                        class="w-10 h-10 flex items-center justify-center bg-yellow-100 text-yellow-700 rounded-xl hover:bg-yellow-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>

                </div>

                <div class="flex justify-end gap-2 pt-5">

                    <button type="button" @click="$dispatch('close-modal', 'reduce-stock')"
                        class="px-3 py-2 text-sm bg-gray-200 rounded-lg">
                        Batal
                    </button>

                    <button type="submit" :disabled="qty > max || max === 0"
                        class="px-4 py-2 text-sm bg-yellow-500 text-white rounded-lg disabled:opacity-50">
                        Kurangi
                    </button>

                </div>

            </form>
        </div>
    </x-modal>

    {{-- modal hapus --}}
    <x-modal name="delete-stock" maxWidth="md">
        <div x-data="{ stockId: null }" x-on:open-modal.window="
            if ($event.detail.name === 'delete-stock') {
                stockId = $event.detail.id
            }" class="p-6">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    Hapus Stok Produk
                </h3>

                <button type="button" @click="$dispatch('close-modal', 'delete-stock')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="text-center space-y-3">
                <p class="text-gray-700 text-md">
                    Apakah kamu yakin ingin menghapus stok produk ini?
                </p>

                <p class="text-sm text-gray-400">
                    Data yang dihapus tidak dapat dikembalikan.
                </p>
            </div>

            <form :action="`/admin/stocks/${stockId}`" method="POST" class="mt-6">
                @csrf
                @method('DELETE')

                <div class="flex gap-3">
                    <button type="button" @click="$dispatch('close-modal', 'delete-stock')"
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