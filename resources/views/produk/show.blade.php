<x-app-layout title="Detail Produk">
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

    <div class="space-y-6 max-w-7xl mx-auto">

        <div x-data class="flex justify-between items-center pt-4 pb-8 border-b border-gray-200">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Terakhir diupdate: {{ $product->updated_at->format('d M Y, H:i') }}
                </p>
            </div>
            @can('edit products')
            <button @click.prevent="if (!canEditProducts) {
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        position: 'top-end',
                        title: 'Kamu tidak punya izin edit produk!',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    $dispatch('open-modal', { name: 'edit-produk'})
                }" class="px-4 py-2 text-white rounded-lg text-sm font-medium {{ auth()->user()->can('edit products')
                        ? 'bg-green-500 hover:bg-green-600 transition-colors shadow-sm'
                        : 'bg-green-200 cursor-not-allowed' }} ">
                Edit
            </button>
            @endcan
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-1">
                <div class="rounded-xl shadow-sm border border-gray-200 h-full lg:max-h-[348px] relative group">

                    <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('image/product/products.jpg') }}"
                        class="w-full h-80 lg:h-full object-cover rounded-lg"
                        onerror="this.src='https://via.placeholder.com/300x400?text=No+Image'">

                    <div x-data
                        class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-200">
                        <button type="button" @click.prevent="
                                if (!canUploadImage) {
                                    Swal.fire({
                                        toast: true,
                                        icon: 'error',
                                        position: 'top-end',
                                        title: 'Kamu tidak punya izin edit gambar!',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                } else {
                                    $dispatch('open-modal', { name: 'edit-image' });
                                }
                            " class="bg-white/90 hover:bg-white text-gray-700 p-2 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-1 text-sm font-medium border border-gray-200 {{ auth()->user()->can('upload product images')
                                ? 'bg-white/90 hover:bg-white text-gray-700 border-gray-200 hover:shadow-xl'
                                : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed opacity-70' }}"">
                            <svg class=" w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                            </svg>
                            Edit
                        </button>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">SKU</p>
                            <p class="text-sm text-gray-500">{{ $product->sku }}</p>
                        </div>
                        <span
                            class="px-3 py-1 bg-green-50 text-green-700 text-sm rounded-full font-medium border border-green-200">
                            {{ $product->category->name ?? '-' }}
                        </span>
                    </div>
                    <div class="bg-green-50/50 p-4 rounded-lg border border-green-100">
                        <p class="text-sm font-medium text-green-800 mb-1">Total Stok</p>
                        <p class="text-xl font-bold text-green-900">{{ $product->stocks->sum('qty') }} pcs</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide mb-3">Harga Satuan</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-gray-900">Rp</span>
                        <span class="text-3xl font-bold text-gray-900 tracking-tight">
                            {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

        </div>


        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-green-500 px-6 py-4 text-white">
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Distribusi Gudang
                </h2>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($product->stocks as $stock)
                <div class="flex justify-between items-center px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $stock->warehouse->name }}</p>
                            <p class="text-sm text-gray-500">Gudang</p>
                        </div>
                    </div>
                    <span
                        class="px-3 py-1 bg-green-50 text-green-700 rounded-lg text-sm font-semibold border border-green-200">
                        {{ $stock->qty }} pcs
                    </span>
                </div>
                @empty
                <div class="text-center py-12 px-6">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada distribusi stok di gudang</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- modal edit foto --}}
    <x-modal name="edit-image" maxWidth="lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Edit Foto Produk</h3>
                <button type="button" @click="$dispatch('close-modal', 'edit-image')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('products.update-image', $product->id) }}" method="POST"
                enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="text-center">
                    <div
                        class="inline-block w-40 h-40 bg-gray-50 rounded-xl p-2 border-2 border-dashed border-gray-200">
                        <img id="image-preview" src="{{ asset('storage/' . $product->image) }}"
                            class="w-full h-full object-cover rounded-lg shadow-md">
                    </div>
                    <p class="text-sm text-gray-500 mt-3">Preview foto saat ini</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload foto baru</label>
                    <input type="file" name="image" id="image-input" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent px-3 py-2"
                        accept="image/*">
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF. Maksimal 2MB</p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="$dispatch('close-modal', 'edit-image')"
                        class="close-modal flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-all">
                        Batal
                    </button>
                    <button type="submit" id="save-btn"
                        class="close-modal flex-1 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium shadow-sm hover:shadow transition-all">
                        Simpan Foto
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- modal edit produk --}}
    <x-modal name="edit-produk" maxWidth="lg" :show="$errors->any()">
        <div x-data x-on:open-modal.window="
        if ($event.detail.name === 'edit-produk') {
            setTimeout(() => $refs.productName.focus(), 100)
        }
    " class="p-6">
            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Informasi Produk</h3>
                    <button type="button" @click="$el.closest('form').reset(); $dispatch('close-modal', 'edit-produk')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ $product->name }}" x-ref="productName"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="price" value="{{ $product->price }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                        <x-input-error :messages="$errors->get('price')" class="mt-2 text-red-500 text-sm" />

                    </div>


                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span
                                class="text-red-500">*</span></label>
                        <select name="category_id"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">

                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' :
                                '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach

                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2 text-red-500 text-sm" />

                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button"
                            @click="$el.closest('form').reset(); $dispatch('close-modal', 'edit-produk')"
                            class="close-modal flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-all">
                            Batal
                        </button>
                        <button type="submit" id="save-btn"
                            class="close-modal flex-1 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium shadow-sm hover:shadow transition-all">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        const canUploadImage = @json(auth()->user()->can('upload product images'));
        const canEditProducts = @json(auth()->user()->can('edit products'));
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image-input');
            const preview = document.getElementById('image-preview');
            const form = document.getElementById('edit-image-form');
            const saveBtn = document.getElementById('save-btn');

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</x-app-layout>