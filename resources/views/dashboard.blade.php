<x-app-layout>
    <div class="min-h-screen">
        <div
            class="bg-white/80 backdrop-blur-md shadow-sm border-b border-green-200 rounded-2xl border border-green-200">
            <div class="max-w-7xl mx-auto px-4 py-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col gap-2">
                        <h1
                            class="text-3xl flex items-center gap-3 font-bold bg-green-600 to-teal-600 bg-clip-text text-transparent">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#16A34A" class="size-8">
                                <path
                                    d="M5.223 2.25c-.497 0-.974.198-1.325.55l-1.3 1.298A3.75 3.75 0 0 0 7.5 9.75c.627.47 1.406.75 2.25.75.844 0 1.624-.28 2.25-.75.626.47 1.406.75 2.25.75.844 0 1.623-.28 2.25-.75a3.75 3.75 0 0 0 4.902-5.652l-1.3-1.299a1.875 1.875 0 0 0-1.325-.549H5.223Z" />
                                <path fill-rule="evenodd"
                                    d="M3 20.25v-8.755c1.42.674 3.08.673 4.5 0A5.234 5.234 0 0 0 9.75 12c.804 0 1.568-.182 2.25-.506a5.234 5.234 0 0 0 2.25.506c.804 0 1.567-.182 2.25-.506 1.42.674 3.08.675 4.5.001v8.755h.75a.75.75 0 0 1 0 1.5H2.25a.75.75 0 0 1 0-1.5H3Zm3-6a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-.75.75h-3a.75.75 0 0 1-.75-.75v-3Zm8.25-.75a.75.75 0 0 0-.75.75v5.25c0 .414.336.75.75.75h3a.75.75 0 0 0 .75-.75v-5.25a.75.75 0 0 0-.75-.75h-3Z"
                                    clip-rule="evenodd" />
                            </svg>

                            Dashboard Toko, @php
                                $user = Auth::user();
                            @endphp
                            {{ $user->store->name }}
                        </h1>
                        <p class="text-green-900 font-medium">Selamat datang kembali!</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto py-6">

            {{-- statis --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                {{-- total order --}}
                <div class="bg-white p-6 rounded-2xl border border-green-200 hover:border-green-500">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-medium text-green-700 uppercase tracking-wide">Total Order</p>
                            <p class="text-3xl font-bold text-green-900">
                                {{ number_format($totalOrder) }}
                            </p>
                            @php
                                $isUp = $percentOrder >= 0;
                            @endphp

                            <p
                                class="font-semibold flex items-center 
                                 {{ $isUp ? 'text-green-600' : 'text-red-600' }}">
                                <i
                                    class="fas {{ $isUp ? 'fa-arrow-up text-green-500' : 'fa-arrow-down text-red-500' }} mr-1"></i>

                                {{ $percentOrderLabel }} dari kemarin
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6 text-white">
                                <path
                                    d="M2.25 2.25a.75.75 0 0 0 0 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 0 0-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 0 0 0-1.5H5.378A2.25 2.25 0 0 1 7.5 15h11.218a.75.75 0 0 0 .674-.421 60.358 60.358 0 0 0 2.96-7.228.75.75 0 0 0-.525-.965A60.864 60.864 0 0 0 5.68 4.509l-.232-.867A1.875 1.875 0 0 0 3.636 2.25H2.25ZM3.75 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM16.5 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- revenue --}}
                <div class="bg-white p-6 rounded-2xl border border-green-200 hover:border-green-500">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-medium text-green-700 uppercase tracking-wide">
                                Revenue Ini
                            </p>

                            <p class="text-3xl font-bold text-green-900 mt-1">
                                {{ 'Rp ' . number_format($todayRevenue, 0, ',', '.') }}
                            </p>

                            @php
                                $isUp = $percentRevenue >= 0;
                            @endphp

                            <p
                                class="font-semibold flex items-center 
                                {{ $isUp ? 'text-green-600' : 'text-red-600' }}">

                                <i
                                    class="fas {{ $isUp ? 'fa-arrow-up text-green-500' : 'fa-arrow-down text-red-500' }} mr-1"></i>

                                {{ $percentRevenueLabel }}
                            </p>
                        </div>

                        <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6 text-white">
                                <path d="M12 7.5a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z" />
                                <path fill-rule="evenodd"
                                    d="M1.5 4.875C1.5 3.839 2.34 3 3.375 3h17.25c1.035 0 1.875.84 1.875 1.875v9.75c0 1.036-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 0 1 1.5 14.625v-9.75ZM8.25 9.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM18.75 9a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75V9.75a.75.75 0 0 0-.75-.75h-.008ZM4.5 9.75A.75.75 0 0 1 5.25 9h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75V9.75Z"
                                    clip-rule="evenodd" />
                                <path
                                    d="M2.25 18a.75.75 0 0 0 0 1.5c5.4 0 10.63.722 15.6 2.075 1.19.324 2.4-.558 2.4-1.82V18.75a.75.75 0 0 0-.75-.75H2.25Z" />
                            </svg>

                        </div>
                    </div>
                </div>

                {{-- stock ready --}}
                <div class="bg-white p-6 rounded-2xl border border-green-200 hover:border-green-500">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-medium text-green-700 uppercase tracking-wide">Stock Siap Jual</p>
                            <p class="text-3xl font-bold text-green-900">
                                {{ number_format($totalStock) }}
                            </p>

                            @php
                                $isUp = $percentStock >= 0;
                            @endphp

                            <p
                                class="font-semibold flex items-center 
                                {{ $isUp ? 'text-green-600' : 'text-orange-600' }}">

                                <i
                                    class="fas {{ $isUp ? 'fa-arrow-up text-green-500' : 'fa-arrow-down text-orange-500' }} mr-1"></i>

                                {{ abs(number_format($percentStock, 1)) }}%
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                    clip-rule="evenodd" />
                            </svg>

                        </div>
                    </div>
                </div>

                {{-- low stock --}}
                <div class="bg-white p-6 rounded-2xl border-2 {{ $isUp ? 'border-green-200' : 'border-amber-200' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-medium text-amber-700 uppercase tracking-wide">Low Stock</p>
                            <p class="text-3xl font-bold text-amber-900">
                                {{ $lowStockCount }}
                            </p>

                            @php
                                $isUp = $percentLow >= 0;
                            @endphp

                            <p
                                class="font-bold text-lg flex items-center gap-2
                                {{ $isUp ? 'text-green-500' : 'text-amber-600' }}">

                                {!! $lowStockCount > 0
                                    ? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                                                            <path fill-rule="evenodd"
                                                                                d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                                                                clip-rule="evenodd" />
                                                                        </svg> Periksa!'
                                    : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                                                            <path fill-rule="evenodd"
                                                                                d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                                                                clip-rule="evenodd" />
                                                                        </svg> Aman' !!}
                            </p>
                        </div>
                        <div
                            class="w-16 h-16 {{ $isUp ? 'bg-green-500' : 'bg-yellow-400' }}  rounded-2xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                    clip-rule="evenodd" />
                            </svg>

                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-1 flex">
                    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm w-full flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6 text-green-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />
                                </svg>

                                Produk Terlaris
                            </h3>
                        </div>

                        <div class="flex-1 flex items-center justify-center">
                            <canvas id="productChart" class="max-h-[400px]"></canvas>
                        </div>

                        <p class="text-xs text-gray-400 text-center mt-3">
                            Berdasarkan jumlah penjualan
                        </p>

                    </div>
                </div>

                <div class="lg:col-span-2 flex">
                    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm w-full flex flex-col">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6 text-green-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                                </svg>

                                Trend Revenue & Order
                            </h3>

                            <form method="GET">
                                <select name="range" onchange="this.form.submit()"
                                    class="w-full appearance-none px-4 py-2 pr-10 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="7" {{ $range == 7 ? 'selected' : '' }}>7 Hari Terakhir
                                    </option>
                                    <option value="30" {{ $range == 30 ? 'selected' : '' }}>30 Hari</option>
                                    <option value="90" {{ $range == 90 ? 'selected' : '' }}>3 Bulan</option>
                                </select>
                            </form>
                        </div>

                        <div class="flex-1">
                            <canvas id="revenueChart" class="w-full h-[260px]"></canvas>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- chart.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                                label: 'Revenue',
                                data: @json($chartRevenue),
                                borderWidth: 3,
                                tension: 0.4
                            },
                            {
                                label: 'Order',
                                data: @json($chartOrders),
                                borderWidth: 3,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if (context.dataset.label === 'Revenue') {
                                            return 'Rp ' + context.raw.toLocaleString('id-ID');
                                        }
                                        return context.raw + ' order';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

    <script>
        const ctx = document.getElementById('productChart');

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: @json($labels),
                datasets: [{
                    data: @json($data),
                    backgroundColor: [
                        '#10b981',
                        '#3b82f6',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '40%'
            }
        });
    </script>
</x-app-layout>
