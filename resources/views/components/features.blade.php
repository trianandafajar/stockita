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
                    <img src="https://images.unsplash.com/photo-1750263160670-42be92c0eaf0?auto=format&fit=crop&w=800&q=80"
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
                    <img src="https://images.unsplash.com/photo-1749244768351-2726dc23d26c?auto=format&fit=crop&w=800&q=80"
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
                    <img src="https://images.unsplash.com/photo-1750262727446-759032bf283f?auto=format&fit=crop&w=800&q=80"
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
                    <img src="https://images.unsplash.com/photo-1782898669120-53aac9b0464e?auto=format&fit=crop&w=800&q=80"
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
                    <img src="https://images.unsplash.com/photo-1746201175390-3e02c20b890b?auto=format&fit=crop&w=800&q=80"
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
                    <img src="https://images.unsplash.com/photo-1667372283496-893f0b1e7c16?auto=format&fit=crop&w=800&q=80"
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
