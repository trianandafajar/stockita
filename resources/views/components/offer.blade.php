@php
$user_id = auth()->id();
$plans = App\Models\Plan::get();
$subscription = App\Models\Subscription::where('user_id', $user_id)->where('status', 'active')->first();
@endphp

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<section id="offer" class="py-24 bg-gray-50 overflow-hidden">
    <div class="max-w-7xl mx-auto px-3 md:px-6">

        <div class="text-center mb-10">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
                Kelola Toko Tanpa Ribet
            </h2>
            <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                Semua kebutuhan inventory, transaksi, dan laporan dalam satu dashboard simpel.
            </p>
        </div>

        <div class="flex justify-center mb-12">
            <div class="bg-gray-100 p-1 rounded-full flex items-center gap-2">
                <button id="monthlyBtn" class="px-5 py-2 rounded-full text-sm font-semibold bg-white shadow">
                    Monthly
                </button>
                <button id="yearlyBtn" class="px-5 py-2 rounded-full text-sm font-semibold text-gray-500">
                    Yearly
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @foreach ($plans as $index => $plan)
            <div class="offer-card w-full
            {{ $plan->name == 'Pro' 
                ? 'bg-emerald-500 text-white p-8 shadow-2xl relative' 
                : 'bg-white p-8 border shadow-sm hover:shadow-xl' }} 
            {{ $loop->last ? 'md:col-span-2 lg:col-span-1' : '' }}
            rounded-3xl transition 
        ">

                {{-- Badge --}}
                @if ($plan->name == 'Pro')
                <span class="absolute top-4 right-4 bg-white text-emerald-600 text-xs px-3 py-1 rounded-full">
                    POPULER
                </span>
                @endif

                @if ($plan->id == 1)
                <span
                    class="absolute top-4 right-4 bg-emerald-100 text-emerald-600 text-xs px-3 py-1 rounded-full font-semibold">
                    FREE TRIAL
                </span>
                @endif

                {{-- Title --}}
                <h3 class="text-xl font-semibold mb-2">{{ $plan->name }}</h3>

                <p class="{{ $plan->name == 'Pro' ? 'opacity-90' : 'text-gray-500' }} mb-6">
                    @if ($plan->name == 'Starter')
                    Untuk bisnis kecil
                    @elseif ($plan->name == 'Business')
                    Untuk skala besar
                    @else
                    Untuk bisnis berkembang
                    @endif
                </p>

                {{-- Price --}}
                <div class="mb-6">
                    <div
                        class="text-sm mt-1 {{ $plan->name == 'Pro' ? 'text-white' : 'text-emerald-500' }} save-badge hidden">
                        Hemat 20%
                    </div>

                    <div class="text-4xl font-bold price" data-monthly="{{ $plan->price }}"
                        data-yearly="{{ $plan->yearly_price }}">
                        Rp {{ $plan->price }}
                    </div>
                </div>

                {{-- Features --}}
                <ul class="space-y-3 mb-8">
                    @foreach ($plan->features as $feature)
                    <li>✔ {{ $feature }}</li>
                    @endforeach
                </ul>

                {{-- Button --}}
                @if ($plan->name == 'Starter')
                <button class="w-full py-3 rounded-xl bg-gray-900 text-white pay-btn" data-plan-id="{{ $plan->id }}">
                    Mulai Gratis
                </button>

                @elseif($plan->name == 'Pro')
                <button class="w-full py-3 rounded-xl bg-white text-emerald-600 font-semibold pay-btn"
                    data-plan-id="{{ $plan->id }}">
                    Pilih Paket
                </button>

                @else
                <button class="w-full py-3 rounded-xl bg-gray-900 text-white pay-btn" data-plan-id="{{ $plan->id }}">
                    Pilih Paket
                </button>
                @endif

            </div>
            @endforeach

        </div>
    </div>
    </div>
</section>

<script>
    const subscription = @json($subscription);
</script>
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

                let value = isYearly ? yearly : monthly;

                el.innerHTML = formatRupiah(value) +
                    (value != 0 ? `<span class="text-lg">/${isYearly ? 'tahun' : 'bulan'}</span>` : '');

                const saveBadge = el.parentElement.querySelector('.save-badge');
                if (saveBadge) {
                    if (isYearly && yearly < monthly * 12) {
                        saveBadge.classList.remove('hidden');
                    } else {
                        saveBadge.classList.add('hidden');
                    }
                }
            });
        }

        function updateButtons() {
            const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

            if (!isLoggedIn) return;

            const currentPlanId = subscription?.plan_id ?? null;
            const currentInterval = subscription?.interval ?? null;

            document.querySelectorAll('.pay-btn').forEach(btn => {
                const planId = parseInt(btn.dataset.planId);

                let isSamePlan = currentPlanId === planId;
                let isDowngrade = currentPlanId && planId < currentPlanId;

                if (isSamePlan) {
                    btn.disabled = true;

                    btn.classList.add('bg-green-500', 'text-green-700', 'cursor-not-allowed');

                    btn.innerText = 'Aktif';

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
                const plan_id = btn.dataset.planId;
                const interval = isYearly ? 'yearly' : 'monthly';

                const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

                if (!isLoggedIn) {
                    window.location.href = `/register?plan=${plan_id}&interval=${interval}`;
                    return;
                }

                try {
                    const res = await fetch(`/pay`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            plan_id,
                            interval
                        })
                    });

                    const data = await res.json();

                    // FREE PLAN
                    if (data.free) {
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            position: 'top-end',
                            title: 'Paket gratis aktif, silahkan login!',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        return;
                    }

                    // MIDTRANS POPUP
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
                                icon: 'warning',
                                position: 'top-end',
                                title: 'Menunggu pembayaran',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        },
                        onError: function(result) {
                            Swal.fire({
                                toast: true,
                                icon: 'error',
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
                        position: 'Terjadi error pada system, tunggu beberapa saat untuk mencoba lagi',
                        title: 'Terjadi error',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        });
        updatePrice();
        updateButtons();
    });
</script>

<script>
    gsap.registerPlugin(ScrollTrigger);

    gsap.set(".offer-card", {
        willChange: "transform, opacity"
    });

    gsap.utils.toArray(".offer-card").forEach((el, i) => {
        gsap.fromTo(el, {
            opacity: 0,
            y: 100,
            scale: 0.96
        }, {
            opacity: 1,
            y: 0,
            scale: 1,
            duration: 0.2,
            ease: "power3.out",
            delay: i * 0.08,
            scrollTrigger: {
                trigger: el,
                start: "top 90%",
                end: "top 60%",
                scrub: 0.8,
            }
        });
    });
</script>