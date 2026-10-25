<section>
    <header class="pb-3 border-b border-[#E2DED2]">
        <h2 class="text-sm font-semibold text-[#1C1A15]">
            Perbarui Kata Sandi
        </h2>

        <p class="mt-0.5 text-xs text-[#6B665A]">
            Pastikan akun Anda menggunakan kata sandi yang aman untuk melindungi data operasional.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4 space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-medium text-[#1C1A15] mb-1">Kata Sandi Saat Ini</label>
            <input id="update_password_current_password" name="current_password" type="password" class="input-text text-xs" autocomplete="current-password">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-medium text-[#1C1A15] mb-1">Kata Sandi Baru</label>
            <input id="update_password_password" name="password" type="password" class="input-text text-xs" autocomplete="new-password">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-medium text-[#1C1A15] mb-1">Konfirmasi Kata Sandi Baru</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="input-text text-xs" autocomplete="new-password">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn btn-primary text-xs">Simpan Kata Sandi</button>

            @if (session('status') === 'password-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs font-medium text-ok"
                >Kata sandi berhasil diperbarui.</span>
            @endif
        </div>
    </form>
</section>
