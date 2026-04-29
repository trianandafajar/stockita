<div class="fixed -bottom-8 left-0 right-0 z-50">
    <div id="cookieConsent"
        class="bg-white border-t-4 max-w-md border-green-500 mx-4 p-5 rounded-lg shadow-lg transform translate-y-full transition-transform duration-500">
        <div class="flex flex-col gap-8">
            <div>
                <p class="text-sm text-gray-700 mb-1">Kami menggunakan cookies untuk pengalaman terbaik</p>
                <p class="text-xs text-gray-500">Dengan melanjutkan, Anda setuju dengan kebijakan kami</p>
            </div>
            <div class="flex gap-2">
                <button id="declineCookie"
                    class="text-gray-500 hover:text-gray-700 text-sm px-4 py-2 hover:bg-gray-100 rounded-md transition-colors">
                    Decline
                </button>
                <button id="acceptCookie"
                    class="bg-green-500 text-white px-6 py-2 rounded-md font-medium hover:bg-green-600 transition-colors">
                    Accept
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cookieConsent = document.getElementById('cookieConsent');
        const acceptBtn = document.getElementById('acceptCookie');
        const declineBtn = document.getElementById('declineCookie');

        if (!localStorage.getItem('cookieAccepted')) {
            setTimeout(() => {
                cookieConsent.classList.remove('translate-y-full');
                cookieConsent.classList.add('mb-12');
            }, 1000);
        }

        acceptBtn.addEventListener('click', () => {
            localStorage.setItem('cookieAccepted', 'true');
            cookieConsent.classList.add('translate-y-full');
            cookieConsent.classList.remove('mb-12');
        });

        declineBtn.addEventListener('click', () => {
            cookieConsent.classList.add('translate-y-full');
            cookieConsent.classList.remove('mb-12');
        });
    });
</script>
