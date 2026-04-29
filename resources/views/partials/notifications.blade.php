@php
$unread = auth()->user()->unreadNotifications;
$notifications = auth()->user()->notifications()->latest()->limit(10)->get();
@endphp

<div x-data="{ open: false }" class="relative">

    <button @click.stop="
        openDropdown = openDropdown === 'notif' ? null : 'notif';

        fetch('/notifications/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });" class="relative p-2 rounded-lg hover:bg-green-50">
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>

        @if($unread->count())
        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
        @endif
    </button>

    <div x-show="openDropdown === 'notif'" @click.outside="openDropdown = null" @click.stop x-transition
        class="fixed inset-x-2 top-17 sm:absolute sm:inset-auto sm:right-0 sm:mt-2 w-auto sm:w-80 bg-white rounded-xl shadow-lg border z-50 overflow-hidden">

        <div class="p-3 font-semibold border-b text-gray-700">
            Notifikasi
        </div>

        <div class="max-h-72 overflow-y-auto">

            @forelse ($notifications as $notif)
            <a href="{{ auth()->user()->hasRole('admin') 
                ? '/admin' . $notif->data['url'] 
                : $notif->data['url'] }}" class="flex items-center justify-between relative p-3 border-b hover:bg-gray-50 transition
                {{ is_null($notif->read_at) ? 'bg-blue-50' : '' }}">

                <div>
                    <p class="text-sm font-semibold text-gray-800 truncate">
                        {{ $notif->data['title'] }}
                    </p>

                    <p class="text-xs text-gray-500 truncate">
                        {{ $notif->data['message'] }}
                    </p>

                    <div class="text-xs text-gray-400 mt-1">
                        {{ $notif->created_at->diffForHumans() }}
                    </div>
                </div>

                <button @click.prevent.stop=" fetch('/notifications/{{ $notif->id }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => {
                    $el.closest('a')?.remove();
                });" class="p-1 rounded-full hover:bg-gray-200 z-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </a>
            @empty
            <p class="p-3 text-sm text-center text-gray-500">
                Tidak ada notifikasi.
            </p>
            @endforelse

        </div>
    </div>
</div>