<header x-data="{ openDropdown: null }" id="headerDashboard"
    class="h-16 fixed top-0 left-0 md:left-64 right-0 z-50 bg-white border-b border-green-100 flex items-center justify-between px-3 md:px-6">
    <div class="flex items-center gap-4">
        <button id="toggleCollapse"
            class="hidden md:flex w-10 h-10 items-center justify-center relative z-[999] pointer-events-auto rounded-lg hover:bg-green-50">
            <svg id="iconMenu" class="w-6 h-6 text-gray-600 absolute md:opacity-0 lg:opacity-1 pointer-events-none"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>

            <svg id="iconArrow" class="w-6 h-6 text-gray-600 absolute opacity-0 pointer-events-none"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </button>

        <button id="toggleSidebar"
            class="md:hidden w-10 h-10 items-center justify-center relative pointer-events-auto rounded-lg hover:bg-green-50">
            <svg class="w-6 h-6 text-gray-600 absolute inset-0 m-auto" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>

        <h1 class="text-lg font-semibold text-gray-800">
            {{ $title ?? 'Dashboard' }}
        </h1>

    </div>

    <div class="flex items-center gap-4">

        {{-- notif --}}
        @include('partials.notifications')

        <div class="relative group inline-block">
            <button @click.stop="openDropdown = openDropdown === 'profile' ? null : 'profile'"
                class="flex items-center gap-2 p-2 rounded-lg hover:bg-green-50">
                <div
                    class="w-8 h-8 bg-green-500 text-white flex items-center justify-center rounded-full text-sm font-semibold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <span class="hidden md:block text-sm font-medium text-gray-700">
                    {{ auth()->user()->name ?? 'User' }}
                </span>
            </button>

            <div x-show="openDropdown === 'profile'" @click.outside="openDropdown = null" @click.stop x-transition
                class="absolute right-0 mt-2 w-64 bg-white/95 backdrop-blur-xl border border-white/50 rounded-2xl shadow-2xl origin-top-right overflow-hidden z-50">

                <div x-data class="py-2 space-y-1">
                    <a href="/profile"
                        class="group/menu flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 hover:text-green-700 transition-all duration-200 relative overflow-hidden">
                        <div
                            class="w-10 h-10 bg-indigo-400 flex items-center justify-center rounded-xl text-white text-sm font-semibold shadow-md group-hover/parent:scale-105 transition-transform duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="font-medium block">Profile</span>
                            <span class="text-xs opacity-75 block">Lihat profil Anda</span>
                        </div>
                        <div
                            class="w-2 h-2 bg-green-400 rounded-full opacity-0 group-hover:opacity-100 transition-opacity ml-auto">
                        </div>
                    </a>

                    <div class="px-4 py-2">
                        <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
                    </div>

                    <button @click="$dispatch('open-modal', { name: 'logout' })"
                        class="w-full flex items-center gap-3 px-5 py-3 text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-rose-50 hover:text-red-700 group/logout transition-all duration-200 relative overflow-hidden">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-red-500 to-rose-600 flex items-center justify-center rounded-xl text-white text-sm font-semibold shadow-md group-hover/logout:scale-105 transition-transform duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1 text-left">
                            <span class="font-medium block">Keluar</span>
                            <span class="text-xs opacity-75 block">Keluar dari akun</span>
                        </div>
                    </button>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>
</header>