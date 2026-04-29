@php
$isAdmin = auth()->user()->hasRole('admin');

$prefix = $isAdmin ? 'admin.' : '';
@endphp
<x-app-layout title="Edit Role - {{ $role->name }}">
    <div class="py-8 mx-auto">
        <!-- Header -->
        <div class="flex flex-col-reverse sm:flex-row sm:items-start sm:justify-between mb-8 gap-4">
            <div>
                <div class="flex items-center gap-4 mb-2">
                    <div
                        class="flex-shrink-0 h-16 w-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit Role</h1>
                        <p class="text-gray-600">Update nama role dan permissions</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route($prefix . 'roles.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="bg-white shadow-2xl rounded-2xl border border-gray-200 overflow-hidden">
            <form method="POST" action="{{ route($prefix . 'roles.update', $role) }}" class="p-8">
                @csrf
                @method('PUT')
                <div class="mb-10">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-3">
                        Nama Role
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}"
                            class="block w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('name') border-red-300 bg-red-50 @enderror"
                            placeholder="Masukkan nama role" required>
                    </div>
                    @error('name')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Permissions ({{ $role->permissions->count() }} terpilih)
                        </h3>
                        <div class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                            {{ $permissions->count() }} total
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($permissions as $permission)
                        <div class="group">
                            <label
                                class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-gray-400 hover:shadow-md transition-all duration-200 cursor-pointer h-full @if ($role->hasPermissionTo($permission->name)) bg-blue-50 border-blue-200 @else bg-white @endif">


                                <div class="flex items-center justify-between w-full ml-10">
                                    <div>
                                        <div class="font-medium text-gray-900 group-hover:text-gray-700">
                                            {{ $permission->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                        </div>
                                    </div>
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="h-5 w-5 text-blue-600 focus:ring-none border-gray-300 rounded-full focus:ring-2"
                                        {{ in_array($permission->name, old('permissions',
                                    $role->permissions->pluck('name')->toArray())) ? 'checked' : '' }}>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-12 pt-8 border-t border-gray-200">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-4 px-6 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Update Role
                    </button>
                    <a href="{{ route($prefix . 'roles.index') }}"
                        class="flex-1 text-center py-4 px-6 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
    <div
        class="fixed top-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-6 py-3 rounded-xl shadow-lg max-w-sm mx-auto">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div
        class="fixed top-4 right-4 z-50 bg-red-100 border border-red-400 text-red-700 px-6 py-3 rounded-xl shadow-lg max-w-sm mx-auto">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</x-app-layout>

<style>
    /* Custom checkbox animation */
    input[type="checkbox"]:checked+div .border-blue-600 {
        background-color: #2563eb;
    }

    /* Toast animation */
    div[style*="fixed"] {
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>

<script>
    // Auto-hide toast messages
    setTimeout(() => {
        const toasts = document.querySelectorAll('div[style*="fixed"]');
        toasts.forEach(toast => {
            toast.style.transition = 'all 0.3s ease-out';
            toast.style.transform = 'translateX(100%)';
            toast.style.opacity = '0';
        });
        setTimeout(() => {
            toasts.forEach(toast => toast.remove());
        }, 300);
    }, 5000);
</script>