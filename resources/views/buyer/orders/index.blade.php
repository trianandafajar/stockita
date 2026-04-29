<x-app-layout title="Histori Pesanan">

    <div class="py-6 space-y-6">

        <h1 class="text-2xl font-bold">Histori Pesanan</h1>


        <form method="GET" class="flex flex-wrap gap-3">

            <select name="status"
                class=" appearance-none px-4 py-2 pr-10 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">Semua Status</option>
                <option value="paid">Paid</option>
                <option value="pending">Pending</option>
            </select>

            <input type="date" name="start"
                class="border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <input type="date" name="end"
                class="border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">

            <button class="bg-emerald-500 text-white px-4 rounded-xl">
                Filter
            </button>
        </form>


        <div class="bg-white rounded-2xl shadow border">

            @forelse ($orders as $order)
                <div class="p-4 border-b flex justify-between items-center">

                    <div>
                        <p class="font-bold">{{ $order->invoice_code }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $order->created_at->format('d M Y H:i') }}
                        </p>
                    </div>

                    <div class="text-right">


                        <p class="font-semibold text-emerald-600">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </p>


                        <span
                            class="text-xs px-2 py-1 rounded-full
                        {{ $order->status == 'paid' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                            {{ strtoupper($order->status) }}
                        </span>

                        <div class="mt-1">
                            <a href="{{ route('buyer.orders.show', $order->id) }}"
                                class="text-xs text-blue-500 underline">
                                Detail
                            </a>
                        </div>

                    </div>
                </div>

            @empty
                <p class="p-4 text-gray-500">Belum ada transaksi</p>
            @endforelse

        </div>

        {{ $orders->links() }}

    </div>

</x-app-layout>
