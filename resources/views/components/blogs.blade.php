<section id="blog" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-3 md:px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900">
                Panduan & Tips Bisnis
            </h2>
            <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                Pelajari cara menggunakan sistem dan tingkatkan penjualan tokomu.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-4">

            <a href="/blog/dashboard-pos"
                class="blog-card group bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col h-full">

                <div class="overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800"
                        class="w-full h-48 md:h-40 lg:h-56 object-cover group-hover:scale-110 transition duration-500">
                </div>

                <div class="p-6 flex flex-col flex-1">
                    <span class="text-sm text-emerald-600 font-semibold">
                        Getting Started
                    </span>

                    <h3 class="text-xl font-bold mt-2 group-hover:text-emerald-600 transition line-clamp-2">
                        Cara Menggunakan Dashboard POS untuk Pertama Kali
                    </h3>

                    <p class="text-gray-600 mt-3 text-sm md:line-clamp-2 lg:line-clamp-none">
                        Pelajari langkah awal mulai dari login hingga melakukan transaksi pertama.
                    </p>
                </div>
            </a>

            <a href="/blog/kelola-produk"
                class="blog-card group bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col h-full">

                <div class="overflow-hidden">
                    <img src="https://media.istockphoto.com/id/1484852942/photo/smart-warehouse-inventory-management-system-concept.webp?a=1&b=1&s=612x612&w=0&k=20&c=0AdwccZASPFJm1I99lN3q9RHz8lNkPUxMOrbHWYfr88="
                        class="w-full h-48 md:h-40 lg:h-56 object-cover group-hover:scale-110 transition duration-500">
                </div>

                <div class="p-6 flex flex-col flex-1">
                    <span class="text-sm text-emerald-600 font-semibold">
                        Produk & Stok
                    </span>

                    <h3 class="text-xl font-bold mt-2 group-hover:text-emerald-600 transition line-clamp-2">
                        Cara Menambahkan dan Mengelola Produk
                    </h3>

                    <p class="text-gray-600 mt-3 text-sm md:line-clamp-2 lg:line-clamp-none">
                        Tambahkan produk baru, atur stok, dan kelola data barang dengan mudah.
                    </p>
                </div>
            </a>

            <a href="/blog/cara-transaksi"
                class="blog-card group bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col h-full">

                <div class="overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1556740772-1a741367b93e?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8dHJhbnNhY3Rpb258ZW58MHx8MHx8fDA%3D"
                        class="w-full h-48 md:h-40 lg:h-56 object-cover group-hover:scale-110 transition duration-500">
                </div>

                <div class="p-6 flex flex-col flex-1">
                    <span class="text-sm text-emerald-600 font-semibold">
                        Transaksi
                    </span>

                    <h3 class="text-xl font-bold mt-2 group-hover:text-emerald-600 transition line-clamp-2">
                        Cara Melakukan Transaksi di Sistem POS (Panduan Kasir)
                    </h3>

                    <p class="text-gray-600 mt-3 text-sm md:line-clamp-2 lg:line-clamp-none">
                        Lakukan transaksi penjualan dengan cepat melalui sistem kasir yang sederhana dan efisien.
                    </p>
                </div>
            </a>

        </div>
    </div>
</section>

<script>
    gsap.registerPlugin(ScrollTrigger);

    gsap.set(".blog-card", {
        willChange: "transform, opacity"
    });

    gsap.utils.toArray(".blog-card").forEach((el, i) => {
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