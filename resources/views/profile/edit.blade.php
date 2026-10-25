<x-app-layout>
    <x-slot name="title">Pengaturan Profil Pengguna</x-slot>

    <div class="space-y-6 max-w-3xl">
        <div class="panel p-5 bg-white">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="panel p-5 bg-white">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="panel p-5 bg-white border-l-4 border-l-danger">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
