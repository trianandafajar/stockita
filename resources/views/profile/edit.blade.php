<x-app-layout title="Profile">
    @if ($message = session('success') ?? (session('error') ?? (session('warning') ?? session('info'))))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let type =
                    "{{ session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : 'info')) }}";

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: "{{ $message }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif
    <div class="mx-auto space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
