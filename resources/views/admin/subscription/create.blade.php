@php
$isAdmin = auth()->user()->hasRole('admin');

$prefix = $isAdmin ? '/admin' : '';
@endphp
<x-app-layout title="Buat Langganan">
    <section class="max-w-7xl mx-auto text-center">
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div class="w-full md:w-1/3">
                <select id="user_id"
                    class="w-full border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-emerald-500">
                    <option value="">Pilih Owner</option>
                    @foreach ($owners as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-auto flex justify-start md:justify-end">
                <div class="bg-gray-100 p-1 rounded-full flex w-full sm:w-auto">

                    <button id="monthlyBtn"
                        class="flex-1 sm:flex-none px-5 py-2 rounded-full text-sm font-semibold bg-white shadow transition">
                        Monthly
                    </button>

                    <button id="yearlyBtn"
                        class="flex-1 sm:flex-none px-5 py-2 rounded-full text-sm font-semibold text-gray-500 transition">
                        Yearly
                    </button>

                </div>
            </div>

        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach ($plans as $plan)
            <div class="offer-card transform hover:-translate-y-1 hover:shadow-xl transition duration-300 p-8  shadow-sm
                        {{ $plan->name == 'Pro' ? 'bg-emerald-500 text-white' : 'bg-white border' }}
                        rounded-3xl relative">
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

                <form onsubmit="return checkUser()" action="{{ $prefix }}/subscriptions" method="POST">
                    @csrf

                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <input type="hidden" name="interval" class="interval-input" value="monthly">
                    <input type="hidden" name="user_id" class="user-input">

                    <button type="submit"
                        class="w-full py-3 rounded-xl
                            {{ $plan->name == 'Pro' ? 'bg-white text-emerald-600 font-semibold' : 'bg-gray-900 text-white' }}">
                        Pilih Paket
                    </button>
                </form>
            </div>
            @endforeach
        </div>

    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let isYearly = false;

            const monthlyBtn = document.getElementById('monthlyBtn');
            const yearlyBtn = document.getElementById('yearlyBtn');
            const prices = document.querySelectorAll('.price');
            const userSelect = document.getElementById('user_id');

            const intervalInputs = document.querySelectorAll('.interval-input');
            const userInputs = document.querySelectorAll('.user-input');

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
                        `<span class="text-lg">/${isYearly ? 'tahun' : 'bulan'}</span>` :
                        '');
                });
            }

            function updateUser() {
                userInputs.forEach(input => {
                    input.value = userSelect.value;
                });
            }

            function updateInterval() {
                intervalInputs.forEach(input => {
                    input.value = isYearly ? 'yearly' : 'monthly';
                });
            }

            monthlyBtn.onclick = () => {
                isYearly = false;
                monthlyBtn.classList.add('bg-white', 'shadow');
                yearlyBtn.classList.remove('bg-white', 'shadow');
                updatePrice();
                updateInterval();
            };

            yearlyBtn.onclick = () => {
                isYearly = true;
                yearlyBtn.classList.add('bg-white', 'shadow');
                monthlyBtn.classList.remove('bg-white', 'shadow');
                updatePrice();
                updateInterval();
            };

            userSelect.addEventListener('change', updateUser);

            updatePrice();
        });

        function checkUser() {
            const user = document.getElementById('user_id').value;
            if (!user) {
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    position: 'top-end',
                    title: 'pilih owner terlebih dahulu!',
                    showConfirmButton: false,
                    timer: 3000
                });
                return false;
            }
            return true;
        }
    </script>
</x-app-layout>