<x-legal-layout>
    <section class="py-16 bg-white min-h-screen">
        <div class="max-w-4xl mx-auto px-4">

            <a href="/"
                class="mb-6 inline-flex items-center gap-2 text-sm text-slate-600 hover:text-emerald-600 transition">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>

                Kembali
            </a>

            <form action="/search" method="GET" class="mb-8">
                <input type="text" name="q" value="{{ $query }}" placeholder="Cari sesuatu..."
                    class="w-full px-5 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
            </form>

            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-slate-900">
                    Hasil pencarian untuk "<span class="text-emerald-600">{{ $query }}</span>"
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ count($results) }} hasil ditemukan
                </p>
            </div>

            @if(count($results) > 0)

            <div class="space-y-6">
                @foreach($results as $item)
                <a href="{{ $item['link'] }}" class="block group">

                    <div class="flex gap-4">
                        <img src="{{ $item['image'] }}" class="w-24 h-24 object-cover rounded-lg flex-shrink-0">

                        <div class="flex-1">
                            <h2 class="text-lg font-semibold text-slate-800 group-hover:text-emerald-600 transition">
                                {{ $item['title'] }}
                            </h2>

                            <p class="text-sm text-slate-500 mt-1">
                                {{ $item['type'] }}
                            </p>

                            <p class="text-sm text-slate-600 mt-2 line-clamp-2">
                                {{ $item['excerpt'] }}
                            </p>
                        </div>

                    </div>

                </a>
                @endforeach
            </div>

            @else

            <div class="text-center py-20">
                <h3 class="text-lg font-medium text-slate-700">
                    Tidak ada hasil
                </h3>
                <p class="text-slate-500 text-sm mt-2">
                    Coba kata kunci lain
                </p>
            </div>

            @endif

        </div>
    </section>
</x-legal-layout>