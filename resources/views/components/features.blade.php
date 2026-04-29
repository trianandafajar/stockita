<section id="features" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-3 md:px-6">

        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-slate-800 mb-4">
                Fitur Unggulan
            </h2>
            <p class="text-slate-600">
                Semua yang kamu butuhkan untuk mengelola toko dengan mudah
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <a href="/features/transaksi-kasir"
                class="feature-item group bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition duration-200">
                <div class="overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1718157582099-5227b1f7112d?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MjB8fFBvaW50JTIwb2YlMjBzZWxsJTIwZGFzaGJvYXJkfGVufDB8fDB8fHww"
                        class="w-full h-56 object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-lg mb-2">POS Modern & Cepat</h3>
                    <p class="text-sm text-gray-600">
                        Sistem kasir real-time untuk transaksi cepat, efisien, dan minim kesalahan.
                    </p>
                </div>
            </a>

            <a href="/features/kelola-produk"
                class="feature-item group bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition duration-200">
                <div class="overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800"
                        class="w-full h-56 object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-lg mb-2">Manajemen Stok Pintar</h3>
                    <p class="text-sm text-gray-600">
                        Pantau stok barang secara otomatis dengan indikator produk hampir habis.
                    </p>
                </div>
            </a>

            <a href="/features/struk-otomatis"
                class="feature-item group bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition duration-200">
                <div class="overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1654263736203-a289f57c0d82?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                        class="w-full h-56 object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-lg mb-2">Struk Otomatis & Profesional</h3>
                    <p class="text-sm text-gray-600">
                        Generate struk transaksi secara otomatis dengan tampilan rapi dan siap cetak.
                    </p>
                </div>
            </a>

            <a href="/features/laporan"
                class="feature-item group bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition duration-200">
                <div class="overflow-hidden">
                    <img src="https://plus.unsplash.com/premium_photo-1661297441050-cd5f9980051d?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8TGFwb3JhbiUyMCUyNiUyMFN0YXRpc3Rpa3xlbnwwfHwwfHx8MA%3D%3D"
                        class="w-full h-56 object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-lg mb-2">Laporan & Statistik</h3>
                    <p class="text-sm text-gray-600">
                        Analisis penjualan dan performa toko dengan dashboard statistik interaktif.
                    </p>
                </div>
            </a>

            <a href="/features/pelanggan"
                class="feature-item group bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition duration-200">
                <div class="overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800"
                        class="w-full h-56 object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-lg mb-2">Manajemen Pelanggan</h3>
                    <p class="text-sm text-gray-600">
                        Kelola data pelanggan dan kirim email promosi atau notifikasi dengan mudah.
                    </p>
                </div>
            </a>

            <a href="/features/multi-role"
                class="feature-item group bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition duration-200">
                <div class="overflow-hidden">
                    <img src="https://plus.unsplash.com/premium_photo-1733328013343-e5ee77acaf05?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTd8fE11bHRpJTIwUm9sZSUyMCUyNiUyMEFrc2VzfGVufDB8fDB8fHww"
                        class="w-full h-56 object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-lg mb-2">Multi Role & Akses</h3>
                    <p class="text-sm text-gray-600">
                        Sistem role Admin, Owner, dan Buyer untuk kontrol penuh dan aman.
                    </p>
                </div>
            </a>
        </div>
    </div>
</section>

<script>
    gsap.registerPlugin(ScrollTrigger);

    gsap.utils.toArray(".feature-item").forEach((el, i) => {
        gsap.fromTo(el, {
            opacity: 0,
            y: 30,
            scale: 0.97
        }, {
            opacity: 1,
            y: 0,
            scale: 1,
            duration: 0.3,
            ease: "power3.out",
            scrollTrigger: {
                trigger: el,
                start: "top 95%",
                toggleActions: "play none none reverse",
            }
        });
    });
</script>