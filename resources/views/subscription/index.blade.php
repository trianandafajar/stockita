<x-app-layout title="Subscription">
    @php
        $plans = $plans ?? [];
    @endphp

    <section class="py-24 bg-gradient-to-b from-white rounded-2xl to-emerald-50">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-4">Kelola Subscription</h2>
            @if ($subscription)
                <p class="text-lg text-gray-600 mb-6">Subscription aktif:
                    <strong>{{ $subscription->plan->name }}</strong>
                    ({{ $subscription->interval }})
                </p>
            @else
                <p class="text-lg text-gray-600 mb-6">Belum memiliki subscription aktif</p>
            @endif

            <div class="flex justify-center mb-12">
                <div class="bg-gray-100 p-1 rounded-full flex items-center gap-2">
                    <button id="monthlyBtn"
                        class="px-5 py-2 rounded-full text-sm font-semibold bg-white shadow">Monthly</button>
                    <button id="yearlyBtn"
                        class="px-5 py-2 rounded-full text-sm font-semibold text-gray-500">Yearly</button>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach ($plans as $plan)
                    <div
                        class="offer-card {{ $plan->name == 'Pro' ? 'bg-emerald-500 text-white p-10 shadow-2xl' : 'bg-white p-8 border shadow-sm hover:shadow-xl' }} rounded-3xl transition relative">
                        @if ($plan->name == 'Pro')
                            <span
                                class="absolute top-4 right-4 bg-white text-emerald-600 text-xs px-3 py-1 rounded-full">POPULER</span>
                        @endif

                        @if ($plan->id == 1)
                            <span
                                class="absolute top-4 right-4 bg-emerald-100 text-emerald-600 text-xs px-3 py-1 rounded-full font-semibold">
                                FREE TRIAL
                            </span>
                        @endif

                        <h3 class="text-xl font-semibold mb-2">{{ $plan->name }}</h3>
                        <p class="mb-6 {{ $plan->name == 'Pro' ? 'opacity-90' : 'text-gray-500' }}">
                            {{ $plan->description }}
                        </p>

                        <div class="text-4xl font-bold mb-6 price" data-monthly="{{ $plan->price }}"
                            data-yearly="{{ $plan->yearly_price }}">
                            Rp {{ $plan->price }}
                        </div>

                        <ul class="space-y-3 mb-8">
                            @foreach ($plan->features as $feature)
                                <li> {{ $feature }}</li>
                            @endforeach
                        </ul>

                        <button
                            class="w-full py-3 rounded-xl transition-all duration-200
                            {{ $plan->name == 'Pro' ? 'bg-white text-emerald-600 font-semibold' : 'bg-gray-900 text-white' }} 
                            pay-btn"
                            data-plan-id="{{ $plan->id }}">
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let isYearly = false;
            const monthlyBtn = document.getElementById('monthlyBtn');
            const yearlyBtn = document.getElementById('yearlyBtn');
            const prices = document.querySelectorAll('.price');

            function formatRupiah(num) {
                if (num == 0) return 'Rp 0';
                return 'Rp ' + (num / 1000) + 'K';
            }

            function updatePrice() {
                prices.forEach(el => {
                    const monthly = el.dataset.monthly;
                    const yearly = el.dataset.yearly;
                    const value = isYearly ? yearly : monthly;
                    el.innerHTML = formatRupiah(value) + (value != 0 ?
                        `<span class="text-lg">/${isYearly ? 'tahun' : 'bulan'}</span>` : '');
                });
            }

            function updateButtons() {
                const currentPlanId = {{ $subscription->plan_id ?? 'null' }};
                const currentInterval = "{{ $subscription->interval ?? '' }}";

                document.querySelectorAll('.pay-btn').forEach(btn => {
                    const planId = parseInt(btn.dataset.planId);

                    let isSamePlan = currentPlanId === planId;
                    let isDowngrade = currentPlanId && planId < currentPlanId;
                    let isSameInterval = currentInterval === (isYearly ? 'yearly' : 'monthly');

                    btn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-300',
                        'text-gray-500');
                    btn.classList.remove('bg-green-100', 'text-green-700');

                    if (isSamePlan) {
                        btn.disabled = true;
                        btn.classList.add('bg-green-500', 'text-green-700', 'cursor-not-allowed');

                        btn.innerText = isSameInterval ? 'Aktif' : 'Paket aktif';

                    } else if (isDowngrade) {
                        btn.disabled = true;

                        btn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed',
                            'opacity-50');

                        btn.innerText = 'Tidak tersedia';

                    } else {
                        btn.disabled = false;

                        btn.innerText = 'Pilih Paket';
                    }
                });
            }

            monthlyBtn.onclick = () => {
                isYearly = false;
                monthlyBtn.classList.add('bg-white', 'shadow');
                yearlyBtn.classList.remove('bg-white', 'shadow');
                updatePrice();
                updateButtons();
            };

            yearlyBtn.onclick = () => {
                isYearly = true;
                yearlyBtn.classList.add('bg-white', 'shadow');
                monthlyBtn.classList.remove('bg-white', 'shadow');
                updatePrice();
                updateButtons();
            };

            document.querySelectorAll('.pay-btn').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const planId = btn.dataset.planId;
                    const interval = isYearly ? 'yearly' : 'monthly';

                    try {
                        const res = await fetch(`/subscription/upgrade`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                plan_id: planId,
                                interval
                            })
                        });

                        const data = await res.json();

                        console.log('ini data dari midtrans', data)

                        if (data.free) {
                            Swal.fire({
                                toast: true,
                                icon: 'success',
                                position: 'top-end',
                                title: 'Paket gratis aktif',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            location.reload();
                            return;
                        }

                        snap.pay(data.snap_token, {
                            onSuccess: function(result) {
                                Swal.fire({
                                    toast: true,
                                    icon: 'success',
                                    position: 'top-end',
                                    title: 'Pembayaran sukses',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                location.reload();
                            },
                            onPending: function(result) {
                                Swal.fire({
                                    toast: true,
                                    icon: 'success',
                                    position: 'top-end',
                                    title: 'Mengunggu pembayaran',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            },
                            onError: function(result) {
                                Swal.fire({
                                    toast: true,
                                    icon: 'success',
                                    position: 'top-end',
                                    title: 'Pembayaran gagal',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        });
                    } catch (err) {
                        console.error(err);
                        Swal.fire({
                            toast: true,
                            icon: 'error',
                            position: 'top-end',
                            title: 'Terjadi error pada system, tunggu beberapa saat untuk mencoba lagi',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                });
            });
            updatePrice()
            updateButtons()
        });
    </script>
</x-app-layout>
