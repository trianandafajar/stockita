<x-app-layout title="Produk">
    @if ($message = session('success') ?? (session('error') ?? (session('warning') ?? session('info'))))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let type = "{{ session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : 'info')) }}";
            
            let message = `{!! $message !!}`; 
            
            let isLongMessage = message.length > 50;

            Swal.fire({
                icon: type,
                title: isLongMessage ? "Peringatan Import" : message,
                html: isLongMessage ? message : "", 
                toast: isLongMessage ? false : true,
                position: isLongMessage ? 'center' : 'top-end',
                showConfirmButton: isLongMessage ? true : false,
                timer: isLongMessage ? null : 3000,
                confirmButtonColor: '#4f46e5',
            });
        });
    </script>
    @endif

    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                    Manajemen Produk
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">
                    Kelola semua produk toko Anda
                </p>
            </div>

            <div x-data class="flex gap-3">
                @can('create products')
                <button @click.prevent="if (!canCreateProducts) {
                        Swal.fire({
                            toast: true,
                            icon: 'error',
                            position: 'top-end',
                            title: 'Kamu tidak punya izin menambah produk!',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        $dispatch('open-modal', { name: 'add-produk'})
                    }" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 text-white font-medium text-sm rounded-xl {{ auth()->user()->can('create products')
                            ? 'bg-green-500 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5'
                            : 'bg-green-200 border-gray-200 cursor-not-allowed' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Produk
                </button>
                @endcan
            </div>
        </div>

        <div x-data class="bg-white rounded-xl border shadow-sm overflow-hidden">

            <form method="GET" action="{{ route('admin.products.index') }}" class="p-4">
                <div class="flex flex-col sm:flex-row gap-3">

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari produk, kategori, nama owner atau nama toko..."
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

                    <a href="{{ route('products.export', request()->query()) }}"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>

                        Export Excel
                    </a>

                    <button @click.prevent="$dispatch('open-modal', { name: 'import-produk'})"
                        class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 flex items-center justify-center gap-2">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>

                        Import Excel
                    </button>

                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>

                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Produk</th>
                            <th class="px-6 py-3">Harga</th>
                            <th class="px-6 py-3">Kategori</th>
                            <th class="px-6 py-3">Toko</th>
                            <th class="px-6 py-3">Pemilik</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse ($products as $product)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="pl-6 pr-12 py-4 flex items-center gap-4 whitespace-nowrap">
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('image/product/products.jpg') }}"
                                    class="w-12 h-12 object-cover rounded-lg border"
                                    onerror="this.src='https://via.placeholder.com/100'">

                                <div>
                                    <p class="font-semibold text-gray-800">
                                        {{ $product->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        SKU: {{ $product->sku }}
                                    </p>
                                </div>
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-700 whitespace-nowrap">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                {{ $product->category->name ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                {{ $product->store->name ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                {{ $product->store->owner->name ?? '-' }}
                            </td>

                            <td x-data class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">

                                    <a href="/admin/products/{{ $product->id }}"
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200">
                                        Detail
                                    </a>

                                    @can('delete products')
                                    <button
                                        class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-600 rounded-lg {{ auth()->user()->can('delete products') ? 'hover:bg-red-100 transition' : 'cursor-not-allowed' }}"
                                        @click="if (!canDeleteProducts) {
                                            Swal.fire({
                                                toast: true,
                                                icon: 'error',
                                                position: 'top-end',
                                                title: 'Kamu tidak punya izin menghapus produk!',
                                                showConfirmButton: false,
                                                timer: 3000
                                            });
                                        } else {
                                            $dispatch('open-modal', { name: 'delete-product', id: {{ $product->id }} })
                                        }">
                                        Hapus
                                    </button>
                                    @endcan
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-400">
                                Belum ada produk
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- delete modal --}}
    <x-modal name="delete-product" maxWidth="md">
        <div x-data="{ productId: null }" x-on:open-modal.window="
            if ($event.detail.name === 'delete-product') {
                productId = $event.detail.id
            }" class="p-6">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    Hapus Produk
                </h3>

                <button type="button" @click="$dispatch('close-modal', 'delete-product')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <p>Apakah anda yakin ingin meghapus produk ini?</p>

            <form :action="`/admin/products/${productId}`" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" @click="$dispatch('close-modal', 'delete-product')"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>

                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-green-700">
                        Hapus
                    </button>
                </div>
            </form>

        </div>
    </x-modal>

    {{-- modal add produk --}}
    <x-modal name="add-produk" maxWidth="xl" :show="$errors->any()">
        <div x-data x-on:open-modal.window="
        if ($event.detail.name === 'add-produk') {
            setTimeout(() => $refs.productName.focus(), 100)
        }
    " class="p-6">
            <form action="/admin/products" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Produk</h3>
                    <button type="button" @click="$el.closest('form').reset(); $dispatch('close-modal', 'add-produk')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" x-ref="productName"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="price" value="{{ old('price') }}"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                            <x-input-error :messages="$errors->get('price')" class="mt-2 text-red-500 text-sm" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Toko <span class="text-red-500">*</span></label>
                        <select name="store_id" id="storeSelect" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">-- Pilih Toko --</option>
                            @foreach ($stores as $store)
                            <option value="{{ $store->id }}">
                                {{ $store->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span
                                class="text-red-500">*</span></label>
                        <select name="category_id" id="categorySelect" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="" disabled>Pilih toko terlebih dahulu!</option>
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar Produk <span
                                class="text-red-500">*</span></label>
                        <div
                            class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-gray-300 hover:bg-gray-50 transition-colors duration-200">
                            <input type="file" name="image" accept="image/*" class="hidden" id="imageUpload">
                            <div id="uploadPlaceholder">
                                <label for="imageUpload" class="cursor-pointer block">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm font-medium text-gray-700 mb-1">Klik untuk upload gambar</p>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF (max 5MB)</p>
                                </label>
                            </div>
                            <div id="imagePreview"
                                class="mx-auto w-60 aspect-square overflow-hidden rounded-lg border hidden cursor-pointer">
                                <img class="w-full h-full object-cover">
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('image')" class="mt-2 text-red-500 text-sm" />

                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button @click.prevent="$el.closest('form').reset(); $dispatch('close-modal', 'add-produk')"
                            class="px-4 py-2 text-sm bg-gray-200 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>

                        <button type="submit"
                            class="px-5 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- modal import --}}
    <x-modal name="import-produk" maxWidth="md" :show="$errors->import->any()">
        <div class="p-6">
            <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Import Produk (Excel)
                    </h3>

                    <button type="button" @click="$dispatch('close-modal', 'import-produk')"
                        class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Toko</label>
                    <select name="store_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        <option value="">-- Pilih Toko --</option>
                        @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>

                <p class="text-sm text-gray-600 mb-4">
                    Upload file Excel (.xlsx / .xls). Pastikan format sesuai template.
                </p>

                <div
                    class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-gray-300 hover:bg-gray-50 transition">

                    <input type="file" name="file" accept=".xlsx,.xls" class="hidden" id="excelUpload">

                    <label for="excelUpload" class="cursor-pointer block">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M12 3v13.5m0 0l-4.5-4.5m4.5 4.5l4.5-4.5" />
                        </svg>

                        <p class="text-sm font-medium text-gray-700 mb-1">
                            Klik untuk upload file Excel
                        </p>

                        <p class="text-xs text-gray-500">
                            Format: .xlsx / .xls (max 5MB)
                        </p>
                    </label>

                    <p id="fileName" class="mt-3 text-sm text-green-600 hidden"></p>
                </div>
                <x-input-error :messages="$errors->import->get('file')" class="mt-2 text-red-500 text-sm" />

                <a href="{{ route('products.template')}}" class="text-sm text-blue-600 hover:underline">
                    Download template Excel
                </a>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" @click="$dispatch('close-modal', 'import-produk')"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        const canCreateProducts = @json(auth()->user()->can('create products'));
        const canDeleteProducts = @json(auth()->user()->can('delete products'));
        const canUploadImage = @json(auth()->user()->can('upload product images'));
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const excelUpload = document.getElementById('excelUpload');
        const fileName = document.getElementById('fileName');

        if (!excelUpload) return;

        excelUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                fileName.textContent = file.name;
                fileName.classList.remove('hidden');
            }
        });
    });
    </script>

    <script>
        document.getElementById('storeSelect').addEventListener('change', function() {
            let storeId = this.value;
            let categorySelect = document.getElementById('categorySelect');

            categorySelect.innerHTML = '<option value="">Loading...</option>';

            fetch(`/admin/categories-by-store/${storeId}`)
                .then(res => res.json())
                .then(data => {
                    categorySelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';

                    data.forEach(cat => {
                        categorySelect.innerHTML += `
                    <option value="${cat.id}">${cat.name}</option>
                `;
                    });
                });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const imageUpload = document.getElementById('imageUpload');
            const imagePreview = document.getElementById('imagePreview');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');

            if (!imageUpload || !imagePreview) return;

            const img = imagePreview.querySelector('img');

            imageUpload.addEventListener('click', function(e) {
                if (!canUploadImage) {
                    e.preventDefault();
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        position: 'top-end',
                        title: 'Kamu tidak punya izin upload gambar!',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });

            imageUpload.addEventListener('change', function(e) {
                if (!canUploadImage) {
                    imageUpload.value = '';
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        position: 'top-end',
                        title: 'Kamu tidak punya izin upload gambar!',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

                const file = e.target.files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        if (img) {
                            img.src = e.target.result;
                        }

                        imagePreview.classList.remove('hidden');
                        uploadPlaceholder?.classList.add('hidden');
                    };

                    reader.readAsDataURL(file);
                }
            });

            imagePreview.addEventListener('click', function() {
                if (!canUploadImage) {
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        position: 'top-end',
                        title: 'Kamu tidak punya izin upload gambar!',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

                imageUpload.click();
            });
        });
    </script>

</x-app-layout>