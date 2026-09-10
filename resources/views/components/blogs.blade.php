<section id="blog" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-3 md:px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900">
                Guides & Business Tips
            </h2>
            <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                Learn how to use the system and boost your store’s sales.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-4">

            <a href="/blog/dashboard-pos"
                class="blog-card group bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col h-full">

                <div class="overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1783408356665-3c0dacbe3d22?auto=format&fit=crop&w=800&q=80"
                        class="w-full h-48 md:h-40 lg:h-56 object-cover group-hover:scale-110 transition duration-500">
                </div>

                <div class="p-6 flex flex-col flex-1">
                    <span class="text-sm text-primary-600 font-semibold">
                        Getting Started
                    </span>

                    <h3 class="text-xl font-bold mt-2 group-hover:text-primary-600 transition line-clamp-2">
                        How to Use the POS Dashboard for the First Time
                    </h3>

                    <p class="text-gray-600 mt-3 text-sm md:line-clamp-2 lg:line-clamp-none">
                        Learn the basics, from logging in to completing your first transaction.
                    </p>
                </div>
            </a>

            <a href="/blog/kelola-produk"
                class="blog-card group bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col h-full">

                <div class="overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1758518727820-28491c194bee?auto=format&fit=crop&w=800&q=80"
                        class="w-full h-48 md:h-40 lg:h-56 object-cover group-hover:scale-110 transition duration-500">
                </div>

                <div class="p-6 flex flex-col flex-1">
                    <span class="text-sm text-primary-600 font-semibold">
                        Products & Inventory
                    </span>

                    <h3 class="text-xl font-bold mt-2 group-hover:text-primary-600 transition line-clamp-2">
                        How to Add and Manage Products
                    </h3>

                    <p class="text-gray-600 mt-3 text-sm md:line-clamp-2 lg:line-clamp-none">
                        Add new products, manage stock, and organize your inventory with ease.
                    </p>
                </div>
            </a>

            <a href="/blog/cara-transaksi"
                class="blog-card group bg-white rounded-3xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col h-full">

                <div class="overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1784639340419-d12a4024cb8c?auto=format&fit=crop&w=800&q=80"
                        class="w-full h-48 md:h-40 lg:h-56 object-cover group-hover:scale-110 transition duration-500">
                </div>

                <div class="p-6 flex flex-col flex-1">
                    <span class="text-sm text-primary-600 font-semibold">
                        Transactions
                    </span>

                    <h3 class="text-xl font-bold mt-2 group-hover:text-primary-600 transition line-clamp-2">
                        How to Process Transactions in the POS System (Cashier Guide)
                    </h3>

                    <p class="text-gray-600 mt-3 text-sm md:line-clamp-2 lg:line-clamp-none">
                        Complete sales transactions quickly using a simple and efficient POS system.
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
