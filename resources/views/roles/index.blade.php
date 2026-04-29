@php
$isAdmin = auth()->user()->hasRole('admin');

$prefix = $isAdmin ? 'admin.' : '';
@endphp
<x-app-layout title="Roles">
    <div class="py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kelola Roles</h1>
                <p class="text-gray-600 mt-1">Daftar semua role dan permission dalam sistem</p>
            </div>
            {{-- <a href="{{ route('roles.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg shadow-md transition-all duration-200 flex items-center gap-2 whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Role
            </a> --}}
        </div>

        {{-- table Container --}}
        <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">
            @if ($roles->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-64">
                                Nama Role
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Permissions
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($roles as $role)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-12 w-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-lg font-semibold text-gray-900">
                                            {{ $role->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $role->permissions->count() }}
                                            permission{{ $role->permissions->count() !== 1 ? 's' : '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if ($role->permissions->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($role->permissions->take(3) as $permission)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                        {{ $permission->name }}
                                    </span>
                                    @endforeach
                                    @if ($role->permissions->count() > 3)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        +{{ $role->permissions->count() - 3 }} lainnya
                                    </span>
                                    @endif
                                </div>
                                @else
                                <span class="text-sm text-gray-400 italic">Tidak ada permission</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route($prefix . 'roles.edit', $role) }}"
                                        class="text-blue-600 hover:text-blue-900 font-medium text-sm p-2 -m-2 rounded-lg hover:bg-blue-50 transition-all duration-200 flex items-center gap-1"
                                        title="Edit Role">
                                        {{-- <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.5h3m1.5-3l-3-3m0 0l-3 3m3-3v2.5A2.5 2.5 0 019.5 20H5a2.5 2.5 0 01-2.5-2.5V8.5A2.5 2.5 0 015 6h2.5a2.5 2.5 0 012.5 2.5v3.5z">
                                            </path>
                                        </svg> --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>

                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada role</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat role pertama Anda.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.roles.create') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Buat Role Pertama
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>