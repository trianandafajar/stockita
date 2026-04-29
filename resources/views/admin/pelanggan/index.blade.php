<x-app-layout title="Pelanggan & Marketing">
    <script>
        const canCreateCustomers = @json(auth()->user()->can('create customers'));
        const canDeleteCustomers = @json(auth()->user()->can('delete customers'))
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
                    Manajemen Pelanggan
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">
                    Kelola semua pelanggan
                </p>
            </div>


            @can('create customers')
            <div x-data class="flex gap-3">
                <button type="button" @click="$dispatch('open-modal', { name: 'import-customer' })"
                    class="px-4 sm:px-6 py-2.5 sm:py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-sm font-medium flex items-center gap-2">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M12 3v13.5m0 0 3-3m-3 3-3-3" />
                    </svg>

                    Import
                </button>
                <button type="button" @click="if (!canCreateCustomers) {
                        Swal.fire({
                            toast: true,
                            icon: 'error',
                            position: 'top-end',
                            title: 'Kamu tidak punya izin menambah pelanggan!',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        $dispatch('open-modal', { name: 'create-customer' })
                    }"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 text-white font-medium text-sm rounded-xl {{ auth()->user()->can('create customers') ? 'bg-green-500 hover:bg-green-600 shadow-lg hover:shadow-xl transition-all' : 'bg-green-200 cursor-not-allowed' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Pelanggan Baru
                </button>
            </div>
            @endcan
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Pelanggan</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ number_format($stats['total']) }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 text-green-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-green-500 p-5 rounded-2xl text-white shadow-md">
                <p class="text-sm opacity-90">Exclusive</p>
                <p class="text-2xl font-bold mt-1">{{ number_format($stats['exclusive']) }}</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500">Pelanggan Aktif</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">
                    {{ number_format($stats['active']) }}
                </p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500">Total Pengeluaran</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">
                    Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}
                </p>
            </div>

        </div>

        {{-- table --}}
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <form method="GET" action="" class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                    <div class="flex flex-wrap gap-3">
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
                                class="w-full appearance-none px-4 py-2 pr-10 border border-gray-200 rounded-xl bg-white">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Tidak
                                    Aktif</option>
                            </select>

                            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </div>

                        <div class="relative w-full sm:w-48">
                            <select name="type"
                                class="w-full appearance-none px-4 py-2 pr-10 border border-gray-200 rounded-lg bg-white">
                                <option value="">Semua Tipe</option>
                                <option value="regular" {{ request('type')=='regular' ? 'selected' : '' }}>Regular
                                </option>
                                <option value="exclusive" {{ request('type')=='exclusive' ? 'selected' : '' }}>
                                    Exclusive</option>
                            </select>

                            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div x-data class="flex items-center gap-3 justify-between flex-1 lg:w-auto">
                        <div class="relative w-full max-w-[600px]">

                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama, email, atau nomor telepon..."
                                class="w-full pl-10 py-3 border border-gray-200 rounded-xl text-sm">

                            <div class="absolute left-2 top-3 text-gray-400 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                                    </path>
                                </svg>
                            </div>

                        </div>

                        <a href="{{ route('customers.export', request()->query()) }}"
                            class="px-6 py-3 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                            </svg>

                            Export
                        </a>

                        <button type="submit" class="px-6 py-3 bg-blue-500 text-white rounded-xl text-sm font-semibold">
                            Filter
                        </button>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Kontak</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Toko</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-r from-green-400 to-green-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-semibold text-sm">
                                            {{ substr($customer->user->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $customer->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $customer->user->email ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <a href="https://wa.me/{{ $customer->phone }}"
                                        class="text-green-600 hover:text-green-700 font-medium flex items-center gap-1">
                                        {{ $customer->formatted_phone }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 {{ $customer->type === 'exclusive' ? 'bg-green-100 text-green-500' : 'bg-gray-100 text-gray-800' }} rounded-full text-xs font-medium">
                                    {{ ucfirst($customer->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 {{ $customer->status === 'active' ? 'bg-green-100 text-green-500' : 'bg-red-100 text-red-800' }} rounded-full text-xs font-medium">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-md">
                                    {{ $customer->store->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div x-data class="flex items-center gap-2">
                                    {{-- <button title="Hapus Pelanggan"
                                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-all"
                                        @click="$dispatch('open-modal', { name: 'delete-customer', id: {{ $customer->id }} })">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                        </svg>

                                    </button> --}}
                                    @can('send customer email')
                                    <form action="/admin/customers/{{ $customer->id }}/send-email" method="POST">
                                        @csrf

                                        <button type="submit"
                                            class="p-1.5 text-gray-400 rounded-lg transition-all {{ auth()->user()->can('send customer email') ? 'hover:text-blue-600 hover:bg-gray-100' : 'cursor-not-allowed opacity-50' }}"
                                            title="Kirim Email" @click.prevent="if (!{{ auth()->user()->can('send customer email') ? 'true' : 'false' }}) {
                                                    Swal.fire({
                                                        toast: true,
                                                        icon: 'error',
                                                        position: 'top-end',
                                                        title: 'Kamu tidak punya izin mengirim email!',
                                                        showConfirmButton: false,
                                                        timer: 3000
                                                    });
                                                } else {
                                                    $el.closest('form').submit();
                                                }">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                            </svg>

                                        </button>
                                    </form>
                                    @endcan

                                    <a title="Detail Pelanggan"
                                        href="{{ route('admin.customers.show', $customer->id) }}"
                                        class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>

                                    </a>

                                    @can('delete customers')
                                    <button title="Hapus Pelanggan"
                                        class="p-1.5 text-gray-400 rounded-lg transition-all 
                                            {{ auth()->user()->can('delete customers') ? 'hover:text-red-600 hover:bg-gray-100' : 'cursor-not-allowed opacity-50' }}"
                                        @click="if (!canDeleteCustomers) {
                                                Swal.fire({
                                                    toast: true,
                                                    icon: 'error',
                                                    position: 'top-end',
                                                    title: 'Kamu tidak punya izin menghapus pelanggan!',
                                                    showConfirmButton: false,
                                                    timer: 3000
                                                });
                                            } else {
                                                $dispatch('open-modal', { name: 'delete-customer', id: {{ $customer->id }} })
                                            }">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr x-data>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold mb-2">Belum ada pelanggan</h3>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t">
                {{ $customers->links() }}
            </div>
        </div>
    </div>

    {{-- create modal --}}
    <x-modal name="create-customer" maxWidth="md" :show="$errors->any()">
        <div class="p-6">
            <form action="/admin/customers" method="POST">
                @csrf
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Tambah Pelanggan Baru</h3>
                    <button type="button"
                        @click="$el.closest('form').reset(); $dispatch('close-modal', 'create-customer')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium">Toko <span class="text-red-500">*</span></label>
                        <select name="store_id" x-model="storeId"
                            class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">

                            <option value="">--- Pilih toko ---</option>

                            @foreach ($stores as $store)
                            <option value="{{ $store->id }}">
                                {{ $store->name }}
                            </option>
                            @endforeach

                        </select>
                        <x-input-error :messages="$errors->get('store')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Pelanggan <span
                                    class="text-red-500">*</span></label>
                            <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="regular" {{ old('type')=='regular' ? 'selected' : '' }}>Regular
                                </option>
                                <option value="exclusive" {{ old('type')=='exclusive' ? 'selected' : '' }}>Exclusive
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2 text-red-500 text-sm" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status <span
                                    class="text-red-500">*</span></label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="active" {{ old('status')=='active' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="inactive" {{ old('status')=='inactive' ? 'selected' : '' }}>Tidak
                                    Aktif
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2 text-red-500 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp <span
                                class="text-red-500">*</span></label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-red-500 text-sm" />
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button"
                            @click="$el.closest('form').reset(); $dispatch('close-modal', 'create-customer')"
                            class="close-modal flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium">
                            Simpan Pelanggan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- delete modal --}}
    <x-modal name="delete-customer" maxWidth="md">
        <div x-data="{ customerId: null }" x-on:open-modal.window="
            if ($event.detail.name === 'delete-customer'    ) {
                customerId = $event.detail.id
            }" class="p-6">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    Hapus Pelanggan
                </h3>

                <button type="button" @click="$dispatch('close-modal', 'delete-customer')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="text-center space-y-3">
                <p class="text-gray-700 text-md">
                    Apakah kamu yakin ingin menghapus pelanggan ini?
                </p>

                <p class="text-sm text-gray-400">
                    Data yang dihapus tidak dapat dikembalikan.
                </p>
            </div>

            <form :action="`/admin/customers/${customerId}`" method="POST" class="mt-6">
                @csrf
                @method('DELETE')

                <div class="flex gap-3">
                    <button type="button" @click="$dispatch('close-modal', 'delete-customer')"
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

    {{-- EMAIL MODAL --}}
    <x-modal name="send-email" max-width="lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-6">Kirim Email</h3>
            <!-- Email form content -->
        </div>
    </x-modal>

    <x-modal name="import-customer" maxWidth="md" :show="$errors->import->any()">
        <div class="p-6">
            <form action="{{ route('admin.customers.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Import Pelanggan</h3>
                    <button type="button" @click="$dispatch('close-modal', 'import-customer')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Target Toko</label>
                        <select name="store_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            <option value="">-- Pilih Toko --</option>
                            @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg text-sm text-blue-700">
                        Format Excel:
                        <ul class="list-disc ml-5 mt-1">
                            <li>nama</li>
                            <li>email</li>
                            <li>no_telepon</li>
                            <li>alamat</li>
                        </ul>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            File Excel <span class="text-red-500">*</span>
                        </label>

                        <input type="file" name="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white
                               file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                               file:text-sm file:font-medium file:bg-blue-50 file:text-blue-600
                               hover:file:bg-blue-100">

                        <x-input-error :messages="$errors->import->get('file')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <a href="{{ route('customers.template')}}" class="text-sm text-blue-600 hover:underline">
                        Download template Excel
                    </a>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="$dispatch('close-modal', 'import-customer')"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>

                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium">
                            Import Data
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>