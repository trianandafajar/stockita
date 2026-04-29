@php
$isAdmin = auth()->user()->hasRole('admin');

$prefix = $isAdmin ? '/admin' : '';
@endphp
<x-app-layout title="Langganan">
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
                    Manajemen Langganan
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">
                    Kelola semua langganan
                </p>
            </div>

            <div x-data class="flex gap-3">
                <button type="button" @click="$dispatch('open-modal', { name: 'import-subscription' })"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 text-white font-medium text-sm rounded-xl bg-blue-500 hover:bg-blue-600 shadow-lg hover:shadow-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 3v13.5m0 0 3-3m-3 3-3-3M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5" />
                    </svg>
                    Import
                </button>

                <a href="{{ $prefix }}/subscriptions/create"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 text-white font-medium text-sm rounded-xl bg-green-500 hover:bg-green-600 shadow-lg hover:shadow-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Langganan
                </a>
            </div>
        </div>

        {{-- table --}}
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <form method="GET" action="" class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                    <div class="flex flex-wrap gap-3">
                        <div class="relative w-full sm:w-48">
                            <select name="interval"
                                class="w-full appearance-none px-4 py-2 pr-10 border border-gray-200 rounded-xl bg-white">
                                <option value="">Semua Interval</option>
                                <option value="monthly" {{ request('status')=='monthly' ? 'selected' : '' }}>Monthly
                                </option>
                                <option value="yearly" {{ request('status')=='yearly' ? 'selected' : '' }}>Yearly
                                </option>
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
                            <select name="status"
                                class="w-full appearance-none px-4 py-2 pr-10 border border-gray-200 rounded-lg bg-white">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="expired" {{ request('status')=='expired' ? 'selected' : '' }}>
                                    Expired
                                </option>
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
                                @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}" {{ request('type')=='$plan->id' ? 'selected' : '' }}>
                                    {{ $plan->name }}
                                </option>
                                @endforeach
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
                                placeholder="Cari nama, atau email..."
                                class="w-full pl-10 py-3 border border-gray-200 rounded-xl text-sm">

                            <div class="absolute left-2 top-3 text-gray-400 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                                    </path>
                                </svg>
                            </div>

                        </div>

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
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Paket</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Interval</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($subscriptions as $subscription)
                        <tr class="hover:bg-gray-50 transition-colors">

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-r from-green-400 to-green-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-semibold text-sm">
                                            {{ substr($subscription->user->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            {{ $subscription->user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $subscription->user->email ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-xs font-medium">
                                    {{ $subscription->plan->name ?? '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ ucfirst($subscription->interval) }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($subscription->created_at)->format('d M Y') }}
                                -
                                {{ \Carbon\Carbon::parse($subscription->current_period_end)->format('d M Y') }}
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 text-xs rounded-full font-medium
                                        {{ $subscription->status === 'expired' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                    {{ $subscription->status }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div x-data class="flex items-center gap-2">

                                    <form action="{{ route('admin.subscriptions.toggle', $subscription->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit" class="p-1.5 rounded-lg transition-all
                                                {{ $subscription->status === 'active'
                                                    ? 'text-yellow-600 hover:bg-yellow-100'
                                                    : 'text-green-600 hover:bg-green-100' }}">

                                            {!! $subscription->status === 'active'
                                            ? '
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor" class="size-5 text-yellow-600">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                                            </svg>
                                            '
                                            : '
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor" class="size-5 text-green-600">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                                            </svg>
                                            ' !!}
                                        </button>
                                    </form>

                                    {{-- upgrade / downgarde --}}
                                    <button @click="$dispatch('open-modal', { 
                                                name: 'upgrade-subscription', 
                                                id: {{ $subscription->id }},
                                                plan_id: {{ $subscription->plan_id }},
                                                interval: '{{ $subscription->interval }}'
                                            })" class="p-1.5 text-yellow-600 hover:bg-yellow-100 rounded-lg"
                                        title="Upgrade / downgrade">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                        </svg>
                                    </button>

                                    <button
                                        @click="$dispatch('open-modal', { name: 'delete-subscription', id: {{ $subscription->id }} })"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-gray-100 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>

                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <h3 class="text-lg font-semibold mb-2">Belum ada langganan</h3>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t">
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>

    {{-- create modal --}}
    <x-modal name="create-subscription" maxWidth="md" :show="$errors->any()">
        <div class="p-6">
            <form action="//customers" method="POST">
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
                        <label class="text-sm font-medium">Toko</label>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Pelanggan *</label>
                            <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="regular" {{ old('type')=='regular' ? 'selected' : '' }}>Regular
                                </option>
                                <option value="exclusive" {{ old('type')=='exclusive' ? 'selected' : '' }}>Exclusive
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2 text-red-500 text-sm" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp</label>
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
    <x-modal name="delete-subscription" maxWidth="md">
        <div x-data="{ subscriptionId: null }" x-on:open-modal.window="
            if ($event.detail.name === 'delete-subscription'    ) {
                subscriptionId = $event.detail.id
            }" class="p-6">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    Hapus Pelanggan
                </h3>

                <button type="button" @click="$dispatch('close-modal', 'delete-subscription')"
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

            <form :action="`{{ $prefix }}/subscriptions/${subscriptionId}`" method="POST" class="mt-6">
                @csrf
                @method('DELETE')

                <div class="flex gap-3">
                    <button type="button" @click="$dispatch('close-modal', 'delete-subscription')"
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

    {{-- upgarede modal --}}
    <x-modal name="upgrade-subscription" maxWidth="md">
        <div x-data="{ subscriptionId: null, planId: '', interval: '' }" x-on:open-modal.window="
        if ($event.detail.name === 'upgrade-subscription') {
            subscriptionId = $event.detail.id
            planId = $event.detail.plan_id
            interval = $event.detail.interval
        }" class="p-6">

            <h3 class="text-lg font-semibold mb-4">Upgrade / Downgrade Paket</h3>

            <form :action="`{{ $prefix }}/subscriptions/${subscriptionId}`" method="POST">
                @csrf
                @method('PUT')

                <select name="plan_id" x-model="planId" class="w-full px-4 py-2 border rounded-lg mb-4">
                    @foreach ($plans as $plan)
                    <option value="{{ $plan->id }}">
                        {{ $plan->name }}
                    </option>
                    @endforeach
                </select>

                <select name="interval" x-model="interval" class="w-full px-4 py-2 border rounded-lg mb-4">
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>

                <div class="flex gap-3">
                    <button type="button" @click="$dispatch('close-modal', 'upgrade-subscription')"
                        class="flex-1 border rounded-lg py-2">
                        Batal
                    </button>

                    <button type="submit" class="flex-1 bg-blue-500 text-white rounded-lg py-2">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- import modal --}}
    <x-modal name="import-subscription" maxWidth="md" :show="$errors->import->any()">
        <div class="p-6">
            <form action="/subscriptions/import" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Import Langganan</h3>
                    <button type="button" @click="$dispatch('close-modal', 'import-subscription')"
                        class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div
                        class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-blue-300 hover:bg-blue-50 transition-colors">
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" id="importFile"
                            onchange="document.getElementById('importFileName').textContent = this.files[0]?.name || 'Belum ada file dipilih'">
                        <label for="importFile" class="cursor-pointer block">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12-3-3m0 0-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            <p class="text-sm font-medium text-gray-700 mb-1">Klik untuk pilih file</p>
                            <p class="text-xs text-gray-400">XLSX, XLS, CSV (maks 2MB)</p>
                        </label>
                        <p id="importFileName" class="text-xs text-blue-600 mt-2 font-medium">Belum ada file dipilih</p>
                    </div>

                    <x-input-error class="text-red-500 text-sm" :messages="$errors->import->get('file')" />

                    <a href="/subscriptions/template" class="text-sm text-blue-600 hover:underline">
                        Download template Excel
                    </a>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="$dispatch('close-modal', 'import-subscription')"
                            class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-medium transition">
                            Import
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </x-modal>
</x-app-layout>