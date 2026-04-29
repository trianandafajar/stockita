<x-app-layout title="Transaksi Baru">
    <div class="space-y-6">

        <div class="flex items-center gap-4">
            <button onclick="history.back()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </button>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Transaksi Baru</h1>
                <p class="text-gray-500">Pilih produk, customer, dan gudang</p>
            </div>
        </div>

        <form id="transactionForm">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <div class="lg:col-span-8 bg-white p-6 rounded-2xl shadow-sm border">

                    <h3 class="font-semibold text-lg mb-4">Pilih Produk</h3>

                    <div class="relative mb-4">
                        <input type="text" id="productSearch" placeholder="Cari produk..."
                            class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500">
                        <div class="absolute left-3 top-4 text-gray-400"><svg xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                    </div>

                    <div id="productsGrid"
                        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 max-h-[60vh] overflow-y-auto">

                        @foreach ($products as $product)
                            <div onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, '{{ asset('storage/' . $product->image) }}')"
                                class="border rounded-xl p-3 cursor-pointer hover:shadow-md hover:border-blue-400 transition">

                                <div
                                    class="h-24 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                                    @if ($product->image)
                                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('image/product/products.jpg') }}"
                                            class="w-full h-full object-contain p-2">
                                    @else
                                        📦
                                    @endif
                                </div>

                                <p class="text-sm font-medium mt-2 truncate">
                                    {{ $product->name }}
                                </p>

                                <p class="text-xs text-gray-500">
                                    Stok: {{ $product->stocks->sum('qty') ?? 0 }}
                                </p>

                                <p class="font-bold text-blue-600">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>

                            </div>
                        @endforeach

                    </div>
                </div>

                <div class="lg:col-span-4 space-y-4">
                    <div class="bg-white p-6 rounded-2xl border">
                        <h4 class="font-semibold mb-3">Customer</h4>

                        <input type="text" id="customerInput" class="w-full border px-3 py-2 rounded-xl"
                            placeholder="Cari / isi nama customer">

                        <input type="hidden" name="customer_id" id="customerId">

                        <input type="hidden" name="customer_name" id="customerName">

                        <div id="customerDropdown" class="mt-2 bg-white border rounded-xl hidden"></div>
                    </div>

                    {{-- cart --}}
                    <div class="bg-white p-6 rounded-2xl border flex flex-col h-[420px]">

                        <h4 id="cartTitle" class="font-semibold mb-3">
                            Keranjang (0)
                        </h4>

                        <div id="cartItems" class="flex-1 overflow-y-auto space-y-2"></div>
                        <div x-data class="border-t pt-3">
                            <div class="flex justify-between text-sm">
                                <span>Total</span>
                                <span id="grandTotal">Rp 0</span>
                            </div>

                            <button id="buttonSubmit" type="button"
                                @click="$dispatch('open-modal', { name: 'payment-modal' })"
                                class="w-full mt-3 bg-green-500 text-white py-3 rounded-xl font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                                Simpan Transaksi
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </form>
    </div>

    <x-modal name="payment-modal" maxWidth="md">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold">Pembayaran</h3>
                <button type="button" @click="$dispatch('close-modal', 'payment-modal')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-sm">Nominal Dibayar</label>
                    <input type="number" id="paid" class="w-full border px-3 py-2 rounded-xl" placeholder="0">
                </div>

                <div>
                    <label class="text-sm">Metode Pembayaran</label>
                    <select id="payment_method" class="w-full border px-3 py-2 rounded-xl">
                        <option value="cash">Cash</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm">Tanggal Bayar</label>
                    <input type="datetime-local" id="paid_at" class="w-full border px-3 py-2 rounded-xl">
                </div>

                <div>
                    <label class="text-sm">Catatan</label>
                    <textarea id="notes" class="w-full border px-3 py-2 rounded-xl"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button @click="$dispatch('close-modal', 'payment-modal')"
                        class="px-4 py-2 bg-gray-200 rounded-xl">
                        Batal
                    </button>
                    <button onclick="submitTransaction()" id="submitBtn"
                        class="px-4 py-2 bg-green-500 text-white rounded-xl flex items-center justify-center gap-2">

                        <span id="btnText">Simpan</span>
                    </button>
                </div>

            </div>
        </div>
    </x-modal>


    <script>
        let cart = [];

        document.addEventListener('DOMContentLoaded', () => {
            updateButtonState();
            document.getElementById('paid_at').value = new Date().toISOString().slice(0, 16);
        });

        function updateButtonState() {
            const button = document.getElementById('buttonSubmit');
            button.disabled = cart.length === 0;
        }

        function addToCart(id, name, price, image = '') {
            let item = cart.find(i => i.id === id);

            if (item) {
                item.qty++;
            } else {
                cart.push({
                    id,
                    name,
                    price,
                    image,
                    qty: 1
                });
            }

            renderCart();
        }

        function changeQty(index, delta) {
            cart[index].qty += delta;

            if (cart[index].qty <= 0) {
                cart.splice(index, 1);
            }

            renderCart();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cartItems');
            const totalEl = document.getElementById('grandTotal');

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-gray-400 py-6">
                        Keranjang kosong
                    </div>`;
                totalEl.textContent = 'Rp 0';
                updateTitle();
                updateButtonState();
                return;
            }

            let total = 0;
            container.innerHTML = '';

            cart.forEach((item, index) => {
                let subtotal = item.price * item.qty;
                total += subtotal;

                container.innerHTML += `
                    <div class="flex items-center gap-2 border p-2 rounded-xl">

                        <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                            ${item.image ? `<img src="${item.image}" class="w-full h-full object-contain">` : '📦'}
                        </div>

                        <div class="flex-1">
                            <p class="text-sm truncate">${item.name}</p>
                            <p class="text-xs text-gray-500">Rp ${format(item.price)}</p>
                        </div>

                        <div class="flex items-center gap-1">
                            <button onclick="changeQty(${index},-1)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                </svg>
                            </button>
                            <span>${item.qty}</span>
                            <button onclick="changeQty(${index},1)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>    
                            </button>
                        </div>

                        <div class="text-sm font-semibold">
                            Rp ${format(subtotal)}
                        </div>

                        <button onclick="removeItem(${index})">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>

                    </div>`;
            });

            totalEl.textContent = 'Rp ' + format(total);
            updateTitle();
            updateButtonState();
        }

        function updateTitle() {
            document.getElementById('cartTitle').innerText =
                `Keranjang (${cart.length})`;
        }

        function format(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        // search
        document.getElementById('productSearch').addEventListener('input', debounce(function(e) {
            const keyword = e.target.value.toLowerCase().trim();
            const productCards = document.querySelectorAll('#productsGrid > div:not(.col-span-full)');
            let visibleCount = 0;

            productCards.forEach(card => {
                const productName = card.querySelector('p.font-medium')?.textContent.toLowerCase() ||
                    '';
                const stockInfo = card.querySelector('p.text-xs')?.textContent.toLowerCase() || '';
                const fullText = (productName + ' ' + stockInfo).trim();

                const isVisible = keyword === '' || fullText.startsWith(keyword);

                card.style.display = isVisible ? 'block' : 'none';

                if (isVisible) visibleCount++;
            });

            const noResultsMsg = document.querySelector('#productsGrid .no-results');
            if (keyword && visibleCount === 0) {
                if (!noResultsMsg) {
                    const noResults = document.createElement('div');
                    noResults.className = 'col-span-full text-center py-20 text-gray-400 no-results';
                    noResults.innerHTML = `
                <svg class="w-24 h-24 mx-auto mb-8 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="text-2xl font-bold text-gray-500 mb-2">Produk tidak ditemukan</h3>
                <p class="text-lg">Coba kata kunci lain atau hapus pencarian</p>
            `;
                    document.getElementById('productsGrid').appendChild(noResults);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
            e.target.style.backgroundColor = keyword ? '#f8fafc' : '';
        }, 300));

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        let isSubmitting = false;

        async function submitTransaction() {
            if (isSubmitting) return;

            isSubmitting = true;

            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            const paid = document.getElementById('paid').value;
            const payment_method = document.getElementById('payment_method').value;
            const paid_at = document.getElementById('paid_at').value;
            const notes = document.getElementById('notes').value;

            try {
                const res = await fetch('/transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        customer_id: document.getElementById('customerId').value,
                        customer_name: document.getElementById('customerName').value,
                        type: 'out',
                        paid: paid,
                        payment_method: payment_method,
                        paid_at: paid_at,
                        notes: notes,
                        items: cart.map(i => ({
                            product_id: i.id,
                            qty: i.qty,
                            price: i.price
                        }))
                    })
                });

                const data = await res.json();

                if (res.ok) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Transaksi berhasil!',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    location.href = '/transactions/' + data.data.id;

                } else {
                    throw data;
                }

            } catch (err) {
                let message = err.message || 'Gagal menyimpan transaksi';

                if (err.errors) {
                    message = Object.values(err.errors).flat().join('<br>');
                }

                Swal.fire({
                    toast: true,
                    icon: 'warning',
                    position: 'top-end',
                    title: 'Transaksi Gagal',
                    html: message,
                    showConfirmButton: false,
                    timer: 3000
                });

                isSubmitting = false;
                btn.disabled = false;
                btn.innerText = 'Simpan';
            }
        }
    </script>

    <script>
        const input = document.getElementById('customerInput');
        const dropdown = document.getElementById('customerDropdown');
        const customerId = document.getElementById('customerId');
        const customerName = document.getElementById('customerName');

        input.addEventListener('input', async () => {
            let q = input.value;

            // reset dulu
            customerId.value = '';
            customerName.value = q;

            if (q.length < 2) {
                dropdown.classList.add('hidden');
                return;
            }

            let res = await fetch(`/customers/search?q=${q}`);
            let data = await res.json();

            dropdown.innerHTML = '';

            if (data.length === 0) {
                dropdown.classList.add('hidden');
                return;
            }

            data.forEach(item => {
                let div = document.createElement('div');
                div.className = "p-2 hover:bg-gray-100 cursor-pointer";
                div.innerText = item.name;

                div.onclick = () => {
                    input.value = item.name;
                    customerId.value = item.id;
                    customerName.value = '';
                    dropdown.classList.add('hidden');
                };

                dropdown.appendChild(div);
            });

            dropdown.classList.remove('hidden');
        });
    </script>

</x-app-layout>
