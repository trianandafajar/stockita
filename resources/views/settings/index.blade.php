<x-app-layout title="Settings">
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
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

    <div class="mx-auto">

        <div class="mb-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Settings</h1>
            @role('admin')
            <p class="text-lg text-gray-600">Kelola konfigurasi aplikasi</p>
            @endrole
            @role('owner')
            <p class="text-lg text-gray-600">Kelola Toko anda</p>
            @endrole
        </div>

        <div class="space-y-8">

            {{-- setting admin --}}
            @role('admin')
            {{-- general --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
                <div x-data class="flex justify-between items-start mb-8">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-1">General</h2>
                        <p class="text-sm text-gray-500">Identitas aplikasi</p>
                    </div>
                    <button @click="$dispatch('open-modal', { name: 'edit-app'})"
                        class="text-sm font-medium text-green-600 hover:text-green-700 px-3 py-1 border border-green-100 rounded-lg hover:bg-green-50 transition-colors">
                        Edit
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Nama Aplikasi</p>
                        <p class="text-2xl font-bold text-gray-900">{{ config('app.name', 'StocKita') }}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Deskripsi</p>
                        <p class="text-lg font-semibold text-gray-900">{{ setting('app.description', 'POS ') }}</p>
                    </div>
                </div>
            </div>

            {{-- plans --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">

                <div x-data class="flex justify-between items-start mb-8">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-1">Plan Settings</h2>
                        <p class="text-sm text-gray-500">Pengaturan paket langganan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    @foreach ($plans as $plan)
                    <div class="border flex flex-col justify-between rounded-xl p-6 relative 
                                    {{ $plan->name == 'Pro' ? 'bg-green-50 border-green-200' : '' }}">

                        {{-- badge --}}
                        @if ($plan->name == 'Pro')
                        <span class="absolute top-3 right-3 text-xs bg-green-600 text-white px-2 py-1 rounded-full">
                            Popular
                        </span>
                        @endif

                        {{-- nama --}}
                        <p class="text-sm uppercase 
                                         {{ $plan->name == 'Pro' ? 'text-green-600' : 'text-gray-500' }}">
                            {{ $plan->name }}
                        </p>

                        {{-- harga --}}
                        <p class="text-2xl font-bold text-gray-900">
                            Rp {{ number_format($plan->price, 0, ',', '.') }}
                        </p>

                        <p class="text-sm text-gray-500">
                            /tahun Rp {{ number_format($plan->yearly_price, 0, ',', '.') }}
                        </p>

                        {{-- durasi --}}
                        <p class="text-sm text-gray-400 mt-1">
                            Berlaku {{ $plan->duration_days }} hari
                        </p>

                        {{-- limit --}}
                        <ul class="mt-4 text-sm text-gray-600 space-y-1">
                            <li>- Produk: {{ $plan->max_products ?? 'Unlimited' }}</li>
                            <li>- Order: {{ $plan->max_orders ?? 'Unlimited' }}</li>
                            <li>- Gudang: {{ $plan->max_warehouses ?? 'Unlimited' }}</li>
                            <li>- Kategori: {{ $plan->max_categories ?? 'Unlimited' }}</li>
                            <li>- Customer: {{ $plan->max_customers ?? 'Unlimited' }}</li>
                        </ul>

                        {{-- fitur --}}
                        @if ($plan->features)
                        <ul class="mt-4 text-sm text-gray-700 space-y-1">
                            @foreach ($plan->features as $feature)
                            <li>✔ {{ $feature }}</li>
                            @endforeach
                        </ul>
                        @endif

                        {{-- action --}}
                        <div x-data class="mt-6 flex gap-2">
                            <button @click="$dispatch('open-modal', { 
                                            name: 'edit-plan',
                                            plan: @js($plan)
                                        })" class="w-full border text-gray-700 py-2 rounded-lg hover:bg-gray-50">
                                Edit
                            </button>
                        </div>

                        <p class="text-xs text-gray-400 mt-3">
                            Ditampilkan ke user di halaman pricing
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- email --}}
            <div x-data class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">

                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Email Template</h2>
                        <p class="text-sm text-gray-500">Kelola template email sistem</p>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    @foreach ($emailTemplates as $template)
                    <div class="bg-white border border-gray-200 rounded-xl p-6">

                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="font-semibold text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $template->key)) }}
                                </h3>
                                <p class="text-xs text-gray-500">
                                    Key: {{ $template->key }}
                                </p>
                            </div>

                            <button @click="$dispatch('open-modal', { 
                            name: 'email-template', 
                            template: @js($template) 
                        })" class="text-sm text-green-600 hover:text-green-700">
                                Edit
                            </button>
                        </div>

                        @php
                        $preview = parse_template($template->body, [
                        'name' => 'Preview User',
                        'store_name' => 'Preview Store',
                        ]);
                        @endphp

                        <div class="bg-gray-50 border rounded-lg p-4 text-sm text-gray-700">
                            {!! $preview !!}
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>
            @endrole

            {{-- setting owner --}}
            @role('owner')
            {{-- store --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm space-y-8">

                {{-- header --}}
                <div x-data class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-1">Store</h2>
                        <p class="text-sm text-gray-500">Informasi toko & pemilik</p>
                    </div>

                    <div class="flex gap-2">
                        {{-- edit --}}
                        @can('edit store')
                        <button @click="$dispatch('open-modal', { name: 'edit-store'})"
                            class="text-sm font-medium text-green-600 hover:text-green-700 px-3 py-1 border border-green-100 rounded-lg hover:bg-green-50">
                            Edit Store
                        </button>
                        @endcan
                    </div>
                </div>

                {{-- STORE INFO --}}
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <p class="text-sm text-gray-500">Nama Toko</p>
                        <p class="font-semibold text-lg">{{ $store?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p>{{ $store?->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">No HP</p>
                        <p>{{ $store?->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Alamat</p>
                        <p>{{ $store?->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- subscribtion --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-1">Subscription</h2>
                    <p class="text-sm text-gray-500">Status paket saat ini</p>
                </div>

                @if ($plan)
                <div class="flex items-center justify-between p-6 border border-gray-100 rounded-xl bg-green-50/50">
                    <div>
                        <p class="text-xl font-bold text-gray-900">{{ $plan->name }} Plan</p>
                        <p class="text-sm text-gray-600">
                            Limit {{ $plan->max_products }} produk • {{ ucfirst($subscription->status) }}
                        </p>
                    </div>
                    <form action="{{ route('subscription.index') }}" method="GET">
                        <button type="submit"
                            class="bg-green-600 text-white px-6 py-2.5 rounded-lg font-medium text-sm hover:bg-green-700 transition-colors shadow-sm hover:shadow-md">
                            Upgrade
                        </button>
                    </form>
                </div>
                @else
                <div class="p-6 border border-gray-100 rounded-xl bg-gray-50 text-center">
                    <p class="text-gray-500">Belum ada paket aktif</p>
                    <a href="{{ route('subscription.index') }}"
                        class="mt-4 inline-block bg-green-600 text-white px-6 py-2.5 rounded-lg font-medium text-sm hover:bg-green-700 transition-colors shadow-sm hover:shadow-md">
                        Pilih Paket
                    </a>
                </div>
                @endif
            </div>
            @endrole
        </div>
    </div>

    {{-- edit app --}}
    @role('admin')
    <x-modal name="edit-app" maxWidth="lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    Edit Informasi Aplikasi
                </h3>

                <button type="button" @click="$dispatch('close-modal', 'edit-app')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="/admin/settings" class="space-y-5">
                @csrf
                <div class="space-y-4">

                    <div>
                        <label class="text-sm font-medium">Nama Aplikasi</label>
                        <input type="text" name="app[name]" value="{{ config('app.name', 'StocKita') }}"
                            class="w-full border px-3 py-2 rounded-xl">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Deskripsi</label>
                        <input type="text" name="app[description]" value="{{ setting('app.description', 'Deskripsi') }}"
                            class="w-full border px-3 py-2 rounded-xl">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t">
                    <button type="button" @click="$dispatch('close-modal', 'edit-app')"
                        class="px-4 py-2 text-sm border rounded-lg">
                        Batal
                    </button>

                    <button type="submit"
                        class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </x-modal>

    {{-- edit plan --}}
    <x-modal name="edit-plan" maxWidth="2xl">
        <div x-data="{ plan: {} }" x-on:open-modal.window="
                    if($event.detail.name === 'edit-plan'){ 
                        plan = $event.detail.plan 
                    }
                " class="p-6">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    Edit Plan
                </h3>

                <button type="button" @click="$dispatch('close-modal', 'edit-plan')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" :action="`/admin/plans/${plan.id}`" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium">Nama Plan</label>
                        <input type="text" name="name" x-model="plan.name" class="w-full border px-3 py-2 rounded-xl">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Harga</label>
                        <input type="number" name="price" x-model="plan.price"
                            class="w-full border px-3 py-2 rounded-xl">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Harga Tahunan</label>
                        <input type="number" name="yearly_price" x-model="plan.yearly_price"
                            class="w-full border px-3 py-2 rounded-xl">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Durasi (hari)</label>
                        <input type="number" name="duration_days" x-model="plan.duration_days"
                            class="w-full border px-3 py-2 rounded-xl">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Max Produk</label>
                        <input type="number" name="max_products" x-model="plan.max_products"
                            class="w-full border px-3 py-2 rounded-xl">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Max Order</label>
                        <input type="number" name="max_orders" x-model="plan.max_orders"
                            class="w-full border px-3 py-2 rounded-xl">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Max Gudang</label>
                        <input type="number" name="max_warehouses" x-model="plan.max_warehouses"
                            class="w-full border px-3 py-2 rounded-xl">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Max Kategori</label>
                        <input type="number" name="max_categories" x-model="plan.max_categories"
                            class="w-full border px-3 py-2 rounded-xl">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Max Customer</label>
                        <input type="number" name="max_customers" x-model="plan.max_customers"
                            class="w-full border px-3 py-2 rounded-xl">
                    </div>

                </div>

                <div>
                    <label class="text-sm font-medium">Features (pisahkan dengan koma)</label>
                    <textarea type="text" name="features" x-text="plan.features ? plan.features.join(', ') : ''"
                        class="w-full h-40 border px-3 py-2 rounded-xl">
                        </textarea>
                </div>
                <div class="flex justify-end gap-2 pt-4 border-t">
                    <button type="button" @click="$dispatch('close-modal', 'edit-plan')"
                        class="px-4 py-2 text-sm border rounded-lg">
                        Batal
                    </button>

                    <button type="submit"
                        class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </x-modal>

    {{-- email templte --}}
    <x-modal name="email-template" maxWidth="2xl">
        <div x-data="{
        template: {},
        quillEditor: null,
        initQuill() {
            this.quillEditor = new Quill('#quill-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['link'],
                        ['clean']
                    ]
                }
            });
        }
        }" x-on:open-modal.window="
            if($event.detail.name === 'email-template'){
                template = $event.detail.template;
                $nextTick(() => {
                    if(!quillEditor) initQuill();

                    let cleaned = (template.body ?? '')
                        .replace(/(<p><br><\/p>\s*){2,}/g, '<p><br></p>')
                        .replace(/(<br>\s*){3,}/g, '<br>');

                    quillEditor.root.innerHTML = cleaned;
                });
            }
            " class="p-6">

            <div class="flex justify-between items-center mb-5 pb-3 border-b">
                <h3 class="text-lg font-semibold">Edit Template Email</h3>
            </div>

            <form method="POST" :action="`/email-template/${template.key}`" class="space-y-6" x-on:submit="
                template.body = quillEditor.root.innerHTML;
                $el.querySelector('#hidden-body').value = template.body;
            ">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-sm font-medium">Subject</label>
                    <input type="text" name="subject" x-model="template.subject"
                        class="mt-2 w-full border rounded-xl px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="text-sm font-medium">Template Email</label>

                    {{-- Quill editor container --}}
                    <div id="quill-editor" class="mt-2 bg-white rounded-xl text-sm min-h-[200px]"></div>

                    {{-- Hidden input untuk submit nilai HTML --}}
                    <input type="hidden" name="body" id="hidden-body">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-green-600 text-white rounded-lg">
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </x-modal>
    @endrole

    {{-- edit toko --}}
    @role('owner')
    <x-modal name="edit-store" maxWidth="lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    Edit Informasi Toko
                </h3>

                <button type="button" @click="$dispatch('close-modal', 'edit-store')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="/settings/store/{{ $store->id }}" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="space-y-4">

                    <div>
                        <label class="text-sm font-medium">Nama Toko</label>
                        <input type="text" name="store[name]" value="{{ $store->name }}"
                            class="w-full border px-3 py-2 rounded-xl">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Email</label>
                        <input type="email" name="store[email]" value="{{ $store->email }}"
                            class="w-full border px-3 py-2 rounded-xl">
                    </div>

                    <div>
                        <label class="text-sm font-medium">No HP</label>
                        <input type="text" name="store[phone]" value="{{ $store->phone }}"
                            class="w-full border px-3 py-2 rounded-xl">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Alamat</label>
                        <textarea name="store[address]"
                            class="w-full h-32 border px-3 py-2 rounded-xl">{{ $store->address }}</textarea>
                    </div>

                </div>

                <div class="flex justify-end gap-2 pt-4 border-t">
                    <button type="button" @click="$dispatch('close-modal', 'edit-store')"
                        class="px-4 py-2 text-sm border rounded-lg">
                        Batal
                    </button>

                    <button type="submit"
                        class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </x-modal>
    @endrole
</x-app-layout>