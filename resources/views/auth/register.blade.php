<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="mx-auto p-2">
        @csrf
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-800">Buat Akun</h2>
            <p class="text-gray-500 text-sm mt-1">Daftar untuk mulai menggunakan aplikasi</p>
        </div>
        <div class="mb-4">
            <x-input-label for="name" :value="__('Name')" class="text-gray-800 font-semibold mb-2 block" />
            <x-text-input id="name"
                class="block w-full border-2 border-green-200 focus:border-green-500 focus:ring-green-500 bg-green-50 text-gray-800 rounded-lg px-4 py-3"
                type="text" name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
        </div>
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" class="text-gray-800 font-semibold mb-2 block" />
            <x-text-input id="email"
                class="block w-full border-2 border-green-200 focus:border-green-500 focus:ring-green-500 bg-green-50 text-gray-800 rounded-lg px-4 py-3"
                type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
        </div>

        <div x-data="{ show: false }" class="mb-4 relative">
            <x-input-label for="password" :value="__('Password')" class="text-gray-800 font-semibold mb-2 block" />

            <x-text-input id="password" x-bind:type="show ? 'text' : 'password'"
                class="block w-full border-2 border-green-200 focus:border-green-500 focus:ring-green-500 bg-green-50 text-gray-800 rounded-lg px-4 py-3 pr-12"
                name="password" required />

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

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
        </div>


        <div x-data="{ showConfirm: false }" class="mb-6 relative">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')"
                class="text-gray-800 font-semibold mb-2 block" />

            <x-text-input id="password_confirmation" x-bind:type="showConfirm ? 'text' : 'password'"
                class="block w-full border-2 border-green-200 focus:border-green-500 focus:ring-green-500 bg-green-50 text-gray-800 rounded-lg px-4 py-3 pr-12"
                name="password_confirmation" required />

            <button type="button" @click="showConfirm = !showConfirm"
                class="absolute right-3 top-[42px] text-gray-500 hover:text-green-600">

                <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>

                <svg x-show="showConfirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
            </button>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-sm" />
        </div>


        <button
            class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 rounded-xl shadow-lg hover:shadow-xl transition-all">
            Register
        </button>


        <p class="text-center text-sm text-gray-600 mt-6">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-semibold">
                Login di sini
            </a>
        </p>
    </form>
    <div class="max-w-md mx-auto mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-500">Atau</span>
            </div>
        </div>


        <a href="/auth/google"
            class="flex items-center justify-center w-full mt-6 mb-6 bg-white border-2 border-green-200 hover:border-green-400 text-gray-800 hover:text-green-700 font-semibold py-3 rounded-xl shadow-md hover:shadow-lg transition-all gap-3">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4"
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853"
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05"
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                <path fill="#EA4335"
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
            </svg>
            Daftar dengan Google
        </a>
    </div>
</x-guest-layout>