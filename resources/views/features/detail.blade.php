<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-slate-900 antialiased">

    <x-landingpage-header />

    <section class="bg-gradient-to-br from-emerald-600 to-emerald-500 text-white py-20 relative overflow-hidden">

        <div class="max-w-5xl mx-auto pb-12 text-center pt-12 px-6">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                {{ $title }}
            </h1>

            <p class="max-w-2xl mx-auto text-lg text-emerald-100 mb-8 leading-relaxed">
                {{ $excerpt ?? 'Fitur ini membantu operasional bisnis jadi lebih cepat dan efisien.' }}
            </p>

            <a href="/register"
                class="inline-block bg-white text-emerald-700 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-50 transition-colors duration-200 shadow-md hover:shadow-lg">
                Mulai Gratis
            </a>
        </div>
    </section>

    <div class="max-w-6xl mx-auto px-6 -mt-20 z-10 relative">
        <img src="{{ $image }}"
            class="w-full h-[450px] md:h-[500px] object-cover rounded-2xl shadow-lg border border-white/20">
    </div>

    @if (!empty($highlights))
    <section class="max-w-6xl mx-auto px-6 mt-10">
        <div class="grid md:grid-cols-3 gap-6 text-center">
            @foreach ($highlights as $item)
            <div class="bg-white p-6 rounded-2xl border hover:border-emerald-200 transition-colors duration-200">
                <div
                    class="w-14 h-14 bg-emerald-600 rounded-2xl flex items-center justify-center mb-6 mx-auto shadow-md">
                    <span class="text-white font-bold text-lg">{{ $loop->index + 1 }}</span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-3">
                    {{ $item['title'] }}
                </h3>
                <p class="text-slate-600">
                    {{ $item['desc'] }}
                </p>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <section class="py-24">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold mb-6 text-slate-900 tracking-tight">
                        Tentang Fitur Ini
                    </h2>
                    <p class="text-lg text-slate-600 leading-relaxed">{{ $description }}</p>
                </div>

                <div class="space-y-4">
                    @foreach ($benefits as $benefit)
                    <div
                        class="flex items-start space-x-3 p-4 bg-white rounded-xl border border-slate-100 hover:border-emerald-200 hover:shadow-sm transition-all duration-200 group">
                        <div
                            class="w-6 h-6 flex-shrink-0 mt-0.5 bg-emerald-100 rounded-lg flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <p class="text-slate-700 font-medium leading-relaxed">{{ $benefit }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-20">
        <div class="max-w-3xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12 text-slate-900">
                Cara Menggunakan
            </h2>
            <div class="relative border-l-2 border-emerald-600 space-y-10">
                @foreach ($steps as $index => $step)
                <div class="relative pl-8 group">
                    <div
                        class="absolute -left-5 top-1 w-10 h-10 bg-emerald-600 text-white flex items-center justify-center rounded-full font-bold shadow-md">
                        {{ $index + 1 }}
                    </div>
                    <h3 class="font-semibold text-lg mb-1 text-slate-900">
                        Langkah {{ $index + 1 }}
                    </h3>
                    <p class="text-slate-600">
                        {{ $step }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    @if (!empty($use_cases))
    <section class="bg-white py-16">
        <div class="max-w-6xl mx-auto px-6">
            <h2 class="text-2xl font-bold text-center mb-10 text-slate-900">
                Cocok Digunakan Untuk
            </h2>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach ($use_cases as $case)
                <div
                    class="bg-slate-50 p-6 rounded-2xl border hover:border-emerald-200 transition-colors duration-200 text-center">
                    <p class="font-semibold text-slate-900">
                        {{ $case }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if (!empty($faqs))
    <section class="py-16">
        <div class="max-w-6xl px-6 mx-auto">
            <h2 class="text-2xl font-bold text-center mb-10 text-slate-900">
                Pertanyaan Umum
            </h2>
            <div class="space-y-4">
                @foreach ($faqs as $faq)
                <details
                    class="bg-white p-6 rounded-xl border hover:border-slate-200 transition-colors duration-200 group">
                    <summary class="cursor-pointer font-semibold text-slate-900 flex items-center justify-between">
                        {{ $faq['q'] }}
                        <svg class="w-5 h-5 text-slate-400 group-open:-rotate-180 transition-transform duration-200"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </summary>
                    <p class="text-slate-600 mt-3 pl-1">
                        {{ $faq['a'] }}
                    </p>
                </details>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="bg-emerald-600 text-white py-20 text-center">
        <h2 class="text-3xl font-bold mb-4">
            Siap Menggunakan Fitur Ini?
        </h2>
        <p class="mb-6 text-emerald-100">
            Mulai sekarang dan rasakan kemudahannya.
        </p>
        <a href="/register"
            class="bg-white text-emerald-600 px-6 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-colors duration-200">
            Mulai Gratis
        </a>
    </section>

    <footer class="border-t border-slate-200 py-6 text-center text-sm text-slate-500">
        © {{ date('Y') }} StocKita. All rights reserved.
    </footer>

</body>

</html>