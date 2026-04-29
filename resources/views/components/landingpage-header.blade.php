<header id="header"
    class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl shadow-lg border-b border-slate-200/50">
    <nav class="max-w-7xl px-3 md:px-6 mx-auto py-4 flex items-center justify-between">
        {{-- logo --}}
        <a href="/" class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg">
                <img src="/image/icon/icon.png" alt="icon">
            </div>
            <div>
                <h1
                    class="font-bold text-2xl bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                    StocKita</h1>
                <p class="text-xs text-slate-500 font-medium">Inventory Pro</p>
            </div>
        </a>

        {{-- nav --}}
        <ul class="hidden lg:flex items-center gap-8 mx-auto">
            <li>
                <a href="/#home"
                    class="text-slate-700 hover:text-emerald-600 font-medium px-3 py-2 rounded-lg transition-all group hover:bg-emerald-50">Home
                </a>
            </li>
            <li>
                <a href="/#features"
                    class="text-slate-700 hover:text-emerald-600 font-medium px-3 py-2 rounded-lg transition-all group hover:bg-emerald-50">Fitur
                </a>
            </li>

            <li>
                <a href="/#blog"
                    class="text-slate-700 hover:text-emerald-600 font-medium px-3 py-2 rounded-lg transition-all group hover:bg-emerald-50">Panduan
                </a>
            </li>
            <li>
                <a href="/#offer"
                    class="text-slate-700 hover:text-emerald-600 font-medium px-3 py-2 rounded-lg transition-all group hover:bg-emerald-50">Harga
                </a>
            </li>
        </ul>
        <div class="flex items-center gap-4">
            <div class="relative hidden lg:block group">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 absolute left-4 top-1/2 -translate-y-1/2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>

                <input id="searchInput" type="text" placeholder="Cari..."
                    class="pl-12 pr-4 py-3 w-72 bg-slate-100 border border-green-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition-all duration-200 shadow-sm group-hover:shadow-md">

                <button id="clearSearch"
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 items-center justify-center hidden transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>

                <div id="searchResults"
                    class="absolute mt-2 w-full bg-white shadow-lg rounded-xl max-h-60 overflow-y-auto hidden z-50 no-scrollbar">
                </div>
            </div>
            <div class="flex items-center gap-4">

                @guest
                <a href="/register"
                    class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 hidden sm:block">
                    Daftar
                </a>

                <a href="/login"
                    class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 hidden sm:block">
                    Masuk
                </a>
                @endguest

                @auth
                <a href="{{ auth()->user()->getDashboardUrl() }}"
                    class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 hidden sm:block">
                    Dashboard
                </a>
                @endauth

            </div>

            <button id="menuToggle"
                class="flex w-10 h-10 items-center justify-center relative z-[9999] pointer-events-auto rounded-lg hover:bg-green-50 lg:hidden">
                <svg id="menuIcon" class="w-6 h-6 text-gray-600 absolute opacity-1 pointer-events-none" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>

                <svg id="closeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-6 h-6 text-gray-600 absolute opacity-0 pointer-events-none">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </nav>

    {{-- mobile menu --}}
    <div id="mobileMenu" class="lg:hidden hidden bg-white border-t border-slate-200 shadow-md overflow-hidden">

        <div class="px-4 pb-4">
            <ul class="flex flex-col gap-2 mt-3">
                <li><a href="/#home" class="block py-2 text-slate-700 hover:text-emerald-600">Home</a></li>
                <li><a href="/#features" class="block py-2 text-slate-700 hover:text-emerald-600">Fitur</a></li>
                <li><a href="/#blog" class="block py-2 text-slate-700 hover:text-emerald-600">Panduan</a></li>
                <li><a href="/#offer" class="block py-2 text-slate-700 hover:text-emerald-600">Harga</a></li>
            </ul>

            {{-- Search mobile --}}
            <div class="relative w-full lg:hidden group">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 absolute left-4 top-1/2 -translate-y-1/2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>

                <input id="searchInput" type="text" placeholder="Cari..."
                    class="pl-12 pr-4 py-3 bg-slate-100 w-full border border-green-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition-all duration-200 shadow-sm group-hover:shadow-md">

                <button id="clearSearch"
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 items-center justify-center hidden transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="absolute left-0 right-0 mt-2 px-2">

                <div id="searchResults"
                    class="w-full bg-white shadow-lg rounded-xl max-h-60 overflow-y-auto hidden z-50">
                    <!-- isi result -->
                </div>

            </div>

            {{-- Button --}}
            <div class="flex items-center gap-4 mt-4 md:hidden">

                @guest
                <a href="/register" class="flex-1 text-center px-4 py-2 bg-emerald-500 text-white rounded-xl">
                    Daftar
                </a>

                <a href="/login" class="flex-1 text-center px-4 py-2 bg-amber-500 text-white rounded-xl">
                    Masuk
                </a>
                @endguest

                @auth
                <a href="{{ auth()->user()->getDashboardUrl() }}"
                    class="flex-1 text-center px-4 py-2 bg-emerald-500 text-white rounded-xl">
                    Dashboard
                </a>
                @endauth

            </div>
        </div>
    </div>
</header>

<style>
    .search-card:hover .title-text {
        color: #059669 !important;
        /* Warna emerald-600 */
    }
</style>