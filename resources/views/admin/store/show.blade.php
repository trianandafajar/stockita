<x-app-layout title="Store Detail">
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

    <div class="space-y-8">

        {{-- HEADER --}}
        <div class="bg-primary-50 p-6 rounded-2xl shadow-sm border border-primary-100">
            <div class="flex justify-between items-center">

                <div>
                    <h1 class="text-3xl font-bold text-primary-800">
                        {{ $store->name }}
                    </h1>
                    <p class="text-primary-600 text-sm mt-1">
                        {{ $store->slug }}
                    </p>
                </div>

                @can('edit store')
                <button x-data @click="$dispatch('open-modal', { name: 'edit-store' })"
                    class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                    Edit
                </button>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- stats --}}
            <div class="lg:col-span-3 grid grid-cols-2 md:grid-cols-4 gap-4">

                <a href="/admin/products?store={{ $store->id }}"
                    class="bg-white p-5 rounded-2xl shadow-sm border hover:shadow-md transition">
                    <p class="text-gray-500 text-xs">Products</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $store->products_count ?? 0 }}
                    </p>
                </a>

                <a href="/admin/transactions?store={{ $store->id }}"
                    class="bg-white p-5 rounded-2xl shadow-sm border hover:shadow-md transition">
                    <p class="text-gray-500 text-xs">Orders</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $store->orders_count ?? 0 }}
                    </p>
                </a>

                <a href="/admin/customers?store={{ $store->id }}"
                    class="bg-white p-5 rounded-2xl shadow-sm border hover:shadow-md transition">
                    <p class="text-gray-500 text-xs">Customers</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $store->customers_count ?? 0 }}
                    </p>
                </a>

                <a href="/admin/warehouse?store={{ $store->id }}"
                    class="bg-white p-5 rounded-2xl shadow-sm border hover:shadow-md transition">
                    <p class="text-gray-500 text-xs">Warehouses</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $store->warehouses_count ?? 0 }}
                    </p>
                </a>

            </div>

            {{-- info --}}
            <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border p-6 space-y-4">

                <h2 class="text-lg font-semibold text-gray-800 mb-2">
                    Store Information
                </h2>

                <div class="space-y-3 text-sm">

                    <div class="flex justify-between">
                        <span class="text-gray-500">Owner</span>
                        <span class="font-medium">{{ $store->owner->name }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Email</span>
                        <span>{{ $store->email }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Phone</span>
                        <span>{{ $store->phone ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span>{{ $store->created_at->format('d M Y') }}</span>
                    </div>

                </div>

                <div class="pt-3 border-t">
                    <p class="text-gray-500 text-sm mb-1">Address</p>
                    <p class="text-gray-700 text-sm">
                        {{ $store->address ?? '-' }}
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- edit modal --}}
    <x-modal name="edit-store" maxWidth="xl" :show="$errors->any()">
        <div class="p-6">
            <form action="/admin/store/{{ $store->id }}" method="POST">
                @csrf
                @method('PUT')

                {{-- HEADER --}}
                <div class="flex justify-between items-center mb-6 pb-4 border-b">
                    <h3 class="text-lg font-semibold">Edit Store</h3>

                    <button type="button" @click="$dispatch('close-modal', 'edit-store')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                {{-- FORM --}}
                <div class="space-y-4">

                    {{-- OWNER --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium">Owner <span class="text-red-500">*</span></label>
                            <input type="text" name="owner_name" class="w-full mt-1 px-4 py-2 border rounded-lg"
                                value="{{ old('owner_name', $store->owner->name) }}">
                            <x-input-error :messages="$errors->get('owner_name')" />
                        </div>

                        <div>
                            <label class="text-sm font-medium">Owner Email <span class="text-red-500">*</span></label>
                            <input type="email" name="owner_email" class="w-full mt-1 px-4 py-2 border rounded-lg"
                                value="{{ old('owner_email', $store->owner->email) }}">
                            <x-input-error :messages="$errors->get('owner_email')" />
                        </div>
                    </div>

                    {{-- STORE --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium">Store Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full mt-1 px-4 py-2 border rounded-lg"
                                value="{{ old('name', $store->name) }}">
                            <x-input-error :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <label class="text-sm font-medium">Store Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" class="w-full mt-1 px-4 py-2 border rounded-lg"
                                value="{{ old('email', $store->email) }}">
                            <x-input-error :messages="$errors->get('email')" />
                        </div>
                    </div>

                    {{-- PHONE --}}
                    <div>
                        <label class="text-sm font-medium">Phone Number</label>
                        <input type="text" name="phone" class="w-full mt-1 px-4 py-2 border rounded-lg"
                            value="{{ old('phone', $store->phone) }}">
                        <x-input-error :messages="$errors->get('phone')" />
                    </div>

                    {{-- ADDRESS --}}
                    <div>
                        <label class="text-sm font-medium">Address</label>
                        <textarea name="address"
                            class="w-full mt-1 px-4 py-2 border rounded-lg">{{ old('address', $store->address) }}</textarea>
                        <x-input-error :messages="$errors->get('address')" />
                    </div>

                    {{-- ACTION --}}
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" @click="$dispatch('close-modal', 'edit-store')"
                            class="px-4 py-2 bg-gray-200 rounded-lg">
                            Cancel
                        </button>

                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Save Changes
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </x-modal>

</x-app-layout>