<section id="features" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-3 md:px-6">

        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-slate-800 mb-4">
                Key Features
            </h2>
            <p class="text-slate-600">
                Everything you need to manage your store with ease
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
                    <h3 class="font-semibold text-lg mb-2">Fast & Modern POS</h3>
                    <p class="text-sm text-gray-600">
                        A real-time cashier system for fast, efficient, and error-free transactions.
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
                    <h3 class="font-semibold text-lg mb-2">Smart Inventory Management</h3>
                    <p class="text-sm text-gray-600">
                        Automatically track stock with low-stock alerts to avoid running out.
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
                    <h3 class="font-semibold text-lg mb-2">Automatic & Professional Receipts</h3>
                    <p class="text-sm text-gray-600">
                        Generate clean, ready-to-print receipts automatically for every transaction.
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
                    <h3 class="font-semibold text-lg mb-2">Reports & Analytics</h3>
                    <p class="text-sm text-gray-600">
                        Analyze sales and store performance with an interactive dashboard.
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
                    <h3 class="font-semibold text-lg mb-2">Customer Management</h3>
                    <p class="text-sm text-gray-600">
                        Manage customer data and send promotions or notifications easily.
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
                    <h3 class="font-semibold text-lg mb-2">Multi Roles & Access Control</h3>
                    <p class="text-sm text-gray-600">
                        Role-based system for Admin, Owner, and Buyer with full control and security.
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