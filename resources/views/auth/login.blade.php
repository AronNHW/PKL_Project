<x-guest-layout>
    <h2 class="text-2xl font-semibold text-ink mb-5">Masuk</h2>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-3.5" x-data="{
        showPassword: false,
        fillUser(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    }">
        @csrf

        <div>
            <label for="email" class="block text-[13px] font-medium text-ink mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', 'admin.pusat@cetakstok.com') }}"
                   required autofocus autocomplete="username" class="input-text py-2">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-[13px] font-medium text-ink">Kata sandi</label>
                <button type="button" @click="showPassword = !showPassword"
                        class="text-xs text-accent hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-accent rounded">
                    <span x-text="showPassword ? 'Sembunyikan' : 'Tampilkan'"></span>
                </button>
            </div>
            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" value="password"
                   required autocomplete="current-password" class="input-text py-2">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="pt-0.5">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer text-xs text-muted">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-[#D4CFC0] text-accent focus:ring-accent w-4 h-4">
                Ingat saya di perangkat ini
            </label>
        </div>

        <button type="submit" class="w-full btn btn-primary py-2 text-sm font-medium">Masuk</button>
    </form>

    <!-- Pilihan akun untuk pengujian peran -->
    <div class="mt-5" x-data>
        <div class="mb-2">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-muted">Pilih Akun Pengguna</h3>
        </div>
        <div class="grid grid-cols-2 gap-2">
            @foreach([
                ['Owner', 'Lihat data seluruh cabang', 'owner@cetakstok.com'],
                ['Admin Pusat', 'Kelola master & logistik pusat', 'admin.pusat@cetakstok.com'],
                ['Admin Cabang 1', 'Operasional unit Cabang 1', 'admin.rungkut@cetakstok.com'],
                ['Admin Cabang 2', 'Operasional unit Cabang 2', 'admin.gubeng@cetakstok.com'],
            ] as [$peran, $keterangan, $email])
                <button type="button"
                        @click="document.getElementById('email').value = '{{ $email }}'; document.getElementById('password').value = 'password'"
                        class="flex flex-col text-left p-2.5 rounded-lg border border-border bg-[#F7F4EC]/60 hover:bg-[#F7F4EC] transition-colors focus:outline-none focus-visible:ring-1 focus-visible:ring-accent">
                    <span class="text-[13px] font-semibold text-ink leading-tight truncate w-full">{{ $peran }}</span>
                    <span class="text-[11px] text-muted leading-tight truncate w-full mt-0.5">{{ $keterangan }}</span>
                </button>
            @endforeach
        </div>
    </div>
</x-guest-layout>
