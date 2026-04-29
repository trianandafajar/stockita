<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Perbarui Kata Sandi') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    <form id="password-form" method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div x-data="{ show: false }" class="mb-4 relative">
            <x-input-label for="update_password_current_password" :value="__('Kata Sandi Saat Ini')"
                class="text-gray-800 font-semibold mb-2 block" />

            <x-text-input id="update_password_current_password" name="current_password"
                x-bind:type="show ? 'text' : 'password'" autocomplete="current-password"
                class="block w-full border-2 border-green-200 focus:border-green-500 focus:ring-green-500 text-gray-800 rounded-lg px-4 py-3 pr-12"
                required />

            <button type="button" @click="show = !show"
                class="absolute right-3 top-[42px] text-gray-500 hover:text-green-600">

                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>

                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>

            </button>

            <x-input-error :messages="$errors->updatePassword->get('current_password')"
                class="mt-2 text-red-500 text-sm" />
        </div>

        <div x-data="{ show: false }" class="mb-4 relative">
            <x-input-label for="update_password_password" :value="__('Kata Sandi Baru')"
                class="text-gray-800 font-semibold mb-2 block" />

            <x-text-input id="update_password_password" name="password" autocomplete="new-password"
                x-bind:type="show ? 'text' : 'password'"
                class="block w-full border-2 border-green-200 focus:border-green-500 focus:ring-green-500 text-gray-800 rounded-lg px-4 py-3 pr-12"
                required />

            <button type="button" @click="show = !show"
                class="absolute right-3 top-[42px] text-gray-500 hover:text-green-600">

                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>

                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>

            </button>

            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-red-500 text-sm" />
        </div>

        <div x-data="{ show: false }" class="mb-4 relative">
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Kata Sandi')"
                class="text-gray-800 font-semibold mb-2 block" />

            <x-text-input id="update_password_password_confirmation" name="password_confirmation"
                autocomplete="new-password" x-bind:type="show ? 'text' : 'password'"
                class="block w-full border-2 border-green-200 focus:border-green-500 focus:ring-green-500 text-gray-800 rounded-lg px-4 py-3 pr-12"
                required />

            <button type="button" @click="show = !show"
                class="absolute right-3 top-[42px] text-gray-500 hover:text-green-600">

                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>

                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>

            </button>

            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')"
                class="mt-2 text-red-500 text-sm" />
        </div>

        <div x-data class="flex items-center gap-4">
            <x-primary-button type="button" @click="
                if (document.getElementById('password-form').reportValidity()) {
                    window.dispatchEvent(new CustomEvent('open-modal', {
                        detail: { name: 'confirm-password-update' }
                    }))
                }
            ">
                Simpan
            </x-primary-button>

            @if (session('status') === 'password-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <x-modal name="confirm-password-update" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    Konfirmasi Perubahan
                </h3>
            </div>

            <p class="text-sm text-gray-600 mb-6">
                Apakah Anda yakin ingin mengubah kata sandi?
            </p>

            <div class="flex gap-3">
                <button type="button" @click="window.dispatchEvent(new CustomEvent('close-modal', {
                    detail: 'confirm-password-update'
                }))" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg">
                    Batal
                </button>

                <button type="submit" form="password-form"
                    class="flex-1 px-4 py-2.5 bg-green-600 text-white rounded-lg">
                    Ya, Perbarui
                </button>
            </div>
        </div>
    </x-modal>
</section>