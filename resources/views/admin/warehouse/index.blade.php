<x-app-layout title="Gudang">
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
    <div class="space-y-4">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Manajemen Gudang</h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Kelola semua Gudang</p>
            </div>

            <div x-data class="flex gap-3">
                @can('create warehouse')
                <button @click="$dispatch('open-modal', { name: 'create-warehouse' })"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 text-white font-medium text-sm rounded-xl bg-green-500 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Gudang
                </button>
                @endcan
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <form method="GET" action="{{ route('admin.warehouse.index') }}" class="p-4">
                <div class="flex flex-col sm:flex-row gap-3">

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari Nama gudang, lokasi, atau nama toko..."
                        class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">

                    <div class="relative w-full sm:w-48">
                        <select name="store"
                            class="w-full appearance-none px-4 py-2 pr-10 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500">

                            <option value="">Semua</option>
                            @foreach ($stores as $store)
                            <option value="{{ $store->id }}" {{ request('store')==$store->id ? 'selected' : '' }}>
                                {{ $store->name }}
                            </option>
                            @endforeach
                        </select>

                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>

                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>

                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="p-3 text-left">Kode</th>
                            <th class="p-3 text-left">Nama</th>
                            <th class="p-3 text-left">Toko</th>
                            <th class="p-3 text-left">Lokasi</th>
                            <th class="p-3 text-left">Deskripsi</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse ($warehouses as $w)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="p-3 font-medium whitespace-nowrap">{{ $w->code }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $w->name }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $w->store->name }}</td>
                            <td class="p-3 text-gray-500 whitespace-nowrap">{{ $w->location }}</td>
                            <td class="p-3 text-gray-500 whitespace-nowrap">{{ $w->description }}</td>

                            <td x-data class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">

                                    <a href="/admin/warehouse/{{ $w->id }}"
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200">
                                        Detail
                                    </a>

                                    @can('edit warehouse')
                                    <button @click="$dispatch('open-modal', { 
                                        name: 'edit-warehouse', 
                                        id: {{ $w->id }},
                                        name_warehouse: '{{ $w->name }}',
                                        location: '{{ $w->location }}',
                                        description: '{{ $w->description }}' 
                                    })"
                                        class="px-3 py-1 text-xs bg-amber-100 text-amber-600 rounded-lg hover:bg-amber-200">
                                        Edit
                                    </button>
                                    @endcan

                                    @can('delete warehouse')
                                    <button
                                        @click="$dispatch('open-modal', { name: 'delete-warehouse', id: {{ $w->id }} })"
                                        class="px-3 py-1 text-xs bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                                        Hapus
                                    </button>
                                    @endcan
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-400">
                                Belum ada gudang
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- create modal --}}
    <x-modal name="create-warehouse" maxWidth="lg" :show="$errors->any()">
        <div class="p-6">
            <form action="/admin/warehouse" method="POST">
                @csrf
                @method('POST')

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Tambah Gudang Baru</h3>
                    <button type="button"
                        @click="$el.closest('form').reset(); $dispatch('close-modal', 'create-warehouse')">
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
                        <x-input-error :messages="$errors->get('store_id')" class="mt-2 text-red-500 text-sm" />
                    </div>


                    <div>
                        <label class="text-sm font-medium">Nama Gudang <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div>
                        <label class="text-sm font-medium">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="location" value="{{ old('location') }}"
                            class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <x-input-error :messages="$errors->get('location')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div>
                        <label class="text-sm font-medium">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea name="description"
                            class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button"
                            @click="$el.closest('form').reset(); $dispatch('close-modal', 'create-warehouse')"
                            class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>

                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- edit Modal --}}
    <x-modal name="edit-warehouse" maxWidth="lg">
        <div class="p-6" x-data="{ id: '', name: '', location: '', description: '' }" @open-modal.window="if($event.detail.name === 'edit-warehouse') {
            id = $event.detail.id;
            name = $event.detail.name_warehouse;
            location = $event.detail.location;
            description = $event.detail.description;
         }">

            <form :action="`/admin/warehouse/${id}`" method="POST">
                @csrf
                @method('PUT')

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Edit Gudang</h3>
                    <button type="button" @click="$dispatch('close-modal', 'edit-warehouse')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium">Nama Gudang <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="name"
                            class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div>
                        <label class="text-sm font-medium">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="location" x-model="location"
                            class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <x-input-error :messages="$errors->get('location')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div>
                        <label class="text-sm font-medium">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea name="description" x-model="description"
                            class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" @click="$dispatch('close-modal', 'edit-warehouse')"
                            class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>

                        <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                            Update Gudang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- delete modal --}}
    <x-modal name="delete-warehouse" maxWidth="md">
        <div x-data="{ warehouseId: null }" x-on:open-modal.window="
            if ($event.detail.name === 'delete-warehouse') {
                warehouseId = $event.detail.id
            }" class="p-6">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    Hapus Gudang
                </h3>

                <button type="button" @click="$dispatch('close-modal', 'delete-warehouse')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="text-center space-y-3">
                <p class="text-gray-700 text-md">
                    Apakah kamu yakin ingin menghapus gudang ini?
                </p>

                <p class="text-sm text-gray-400">
                    Data yang dihapus tidak dapat dikembalikan.
                </p>
            </div>

            <form :action="`/admin/warehouse/${warehouseId}`" method="POST" class="mt-6">
                @csrf
                @method('DELETE')

                <div class="flex gap-3">
                    <button type="button" @click="$dispatch('close-modal', 'delete-warehouse')"
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