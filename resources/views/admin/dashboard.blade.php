<x-app-layout>
    <div class="min-h-screen">

        {{-- header --}}
        <div
            class="bg-white/80 backdrop-blur-md shadow-sm border-b border-green-200 rounded-2xl border">
            <div class="max-w-7xl">
                <div class="flex items-center justify-between">
                    <div class="px-6 py-6">
                        <h1 class="text-3xl font-bold text-green-600">
                            Dashboard Admin
                        </h1>
                        <p class="text-green-900">Monitoring seluruh platform</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto py-6">

            {{-- cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                {{-- total user --}}
                <div class="bg-white p-6 rounded-2xl border border-green-200 hover:border-green-500">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-medium text-green-700 uppercase tracking-wide">Total Users</p>
                            <p class="text-3xl font-bold text-green-900">
                                {{ number_format($totalUsers) }}
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6 text-white">
                                <path fill-rule="evenodd"
                                    d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z"
                                    clip-rule="evenodd" />
                                <path
                                    d="M5.082 14.254a8.287 8.287 0 0 0-1.308 5.135 9.687 9.687 0 0 1-1.764-.44l-.115-.04a.563.563 0 0 1-.373-.487l-.01-.121a3.75 3.75 0 0 1 3.57-4.047ZM20.226 19.389a8.287 8.287 0 0 0-1.308-5.135 3.75 3.75 0 0 1 3.57 4.047l-.01.121a.563.563 0 0 1-.373.486l-.115.04c-.567.2-1.156.349-1.764.441Z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- total store --}}
                <div class="bg-white p-6 rounded-2xl border border-green-200 hover:border-green-500">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-medium text-green-700 uppercase tracking-wide">
                                Total Stores
                            </p>

                            <p class="text-3xl font-bold text-green-900 mt-1">
                                {{ number_format($totalStores) }}
                            </p>
                        </div>

                        <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6 text-white">
                                <path
                                    d="M5.223 2.25c-.497 0-.974.198-1.325.55l-1.3 1.298A3.75 3.75 0 0 0 7.5 9.75c.627.47 1.406.75 2.25.75.844 0 1.624-.28 2.25-.75.626.47 1.406.75 2.25.75.844 0 1.623-.28 2.25-.75a3.75 3.75 0 0 0 4.902-5.652l-1.3-1.299a1.875 1.875 0 0 0-1.325-.549H5.223Z" />
                                <path fill-rule="evenodd"
                                    d="M3 20.25v-8.755c1.42.674 3.08.673 4.5 0A5.234 5.234 0 0 0 9.75 12c.804 0 1.568-.182 2.25-.506a5.234 5.234 0 0 0 2.25.506c.804 0 1.567-.182 2.25-.506 1.42.674 3.08.675 4.5.001v8.755h.75a.75.75 0 0 1 0 1.5H2.25a.75.75 0 0 1 0-1.5H3Zm3-6a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-.75.75h-3a.75.75 0 0 1-.75-.75v-3Zm8.25-.75a.75.75 0 0 0-.75.75v5.25c0 .414.336.75.75.75h3a.75.75 0 0 0 .75-.75v-5.25a.75.75 0 0 0-.75-.75h-3Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- total transaksi --}}
                <div
                    class="md:col-span-2 lg:col-span-1 bg-white p-6 rounded-2xl border border-green-200 hover:border-green-500">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-medium text-green-700 uppercase tracking-wide">Total Transactions</p>
                            <p class="text-3xl font-bold text-green-900">
                                {{ number_format($totalTransactions) }}
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
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 md:gap-6 mb-6">
                <div
                    class="bg-white justify-between flex flex-col p-6 rounded-2xl lg:col-span-2 border shadow-sm transition group">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-sm text-gray-500">Total Revenue</p>
                            <div class="bg-green-100 p-2 rounded-lg group-hover:scale-110 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                            </div>
                        </div>

                        <p class="text-3xl font-bold text-green-600">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </p>

                        <p class="text-sm mt-2 {{ $revenueGrowth >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $revenueGrowth >= 0 ? '▲' : '▼' }}
                            {{ number_format($revenueGrowth, 1) }}% dari periode sebelumnya
                        </p>
                    </div>
                    <canvas id="miniRevenueChart"></canvas>
                </div>

                <div class="bg-white p-6 lg:col-span-3 rounded-2xl border">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold">Trend Revenue & Orders</h3>

                        <form method="GET">
                            <select name="range" onchange="this.form.submit()"
                                class="w-full appearance-none px-4 py-2 pr-10 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="7" {{ $range == 7 ? 'selected' : '' }}>7 Hari</option>
                                <option value="30" {{ $range == 30 ? 'selected' : '' }}>30 Hari</option>
                                <option value="90" {{ $range == 90 ? 'selected' : '' }}>90 Hari</option>
                            </select>
                        </form>
                    </div>

                    <div class="h-72">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- top store --}}
                <div class="bg-white p-6 rounded-2xl border">
                    <h3 class="font-semibold mb-4">Top Stores</h3>
                    <canvas id="topStoreChart"></canvas>
                </div>

                {{-- transaksi --}}
                <div class="bg-white p-6 rounded-2xl border">
                    <h3 class="font-semibold mb-4">Recent Transactions</h3>

                    @foreach ($latestTransactions as $trx)
                        <div class="flex items-start gap-3 pb-2 border-b  mb-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>

                            <div class="flex-1">
                                <p class="text-sm font-medium">{{ $trx->invoice_code }}</p>
                                <p class="text-xs text-gray-500">{{ $trx->store->name }}</p>
                            </div>

                            <span class="text-sm font-semibold">
                                Rp {{ number_format($trx->total) }}
                            </span>
                        </div>
                    @endforeach
                </div>

            </div>

        </div>
    </div>

    {{-- chart js --}}
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
                                label: 'Order',
                                data: @json($chartOrders),
                                borderWidth: 3,
                                tension: 0.4,
                                yAxisID: 'yOrder'
                            },
                            {
                                label: 'Revenue',
                                data: @json($chartRevenue),
                                borderWidth: 3,
                                tension: 0.4,
                                yAxisID: 'yRevenue'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            yOrder: {
                                type: 'linear',
                                position: 'left',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Order'
                                }
                            },
                            yRevenue: {
                                type: 'linear',
                                position: 'right',
                                beginAtZero: true,
                                grid: {
                                    drawOnChartArea: false
                                },
                                title: {
                                    display: true,
                                    text: 'Revenue (Rp)'
                                }
                            }
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

            new Chart(document.getElementById('miniRevenueChart'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        data: @json($chartRevenue),
                        borderColor: '#22c55e',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                }
            });

            new Chart(document.getElementById('topStoreChart'), {
                type: 'bar',
                data: {
                    labels: @json($topStores->pluck('store.name')),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($topStores->pluck('revenue')),
                        borderWidth: 1
                    }]
                }
            });
        });
    </script>

</x-app-layout>
