<section>
    <header class="pb-3 border-b border-[#E2DED2]">
        <h2 class="text-sm font-semibold text-[#1C1A15]">
            Informasi Profil Akun
        </h2>

        <p class="mt-0.5 text-xs text-[#6B665A]">
            Perbarui nama pengguna dan alamat email terdaftar Anda di sistem.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4 space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-xs font-medium text-[#1C1A15] mb-1">Nama Lengkap</label>
            <input id="name" name="name" type="text" class="input-text text-xs" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-xs font-medium text-[#1C1A15] mb-1">Alamat Email</label>
            <input id="email" name="email" type="email" class="input-text text-xs font-mono" value="{{ old('email', $user->email) }}" required autocomplete="username">
            <x-input-error class="mt-1" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-xs text-[#6B665A]">
                        Alamat email Anda belum terverifikasi.

                        <button form="send-verification" class="underline text-xs text-[#0B6B73] hover:text-[#173EB0]">
                            Kirim ulang tautan verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 font-medium text-xs text-ok">
                            Tautan verifikasi baru telah dikirimkan ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn btn-primary text-xs">Simpan Perubahan</button>

            @if (session('status') === 'profile-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs font-medium text-ok"
                >Perubahan profil berhasil disimpan.</span>
            @endif
        </div>
    </form>
</section>
