<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StocKita - Inventory & POS Modern</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'bounce-slow': 'bounce 3s infinite',
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .hero-slide {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-emerald-50 overflow-x-hidden">

    <x-landingpage-header />

    <x-carousel />

    <x-sponsorship />

    {{-- features --}}
    <x-features />

    {{-- blog --}}
    <x-blogs />

    {{-- subscribe --}}
    <x-offer />

    {{-- footer --}}
    <x-footer />

    <x-cookie-consent />
    <script></script>
</body>

</html>