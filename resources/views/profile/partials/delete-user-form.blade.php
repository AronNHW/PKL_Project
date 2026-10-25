<section class="space-y-4">
    <header class="pb-3 border-b border-[#E2DED2]">
        <h2 class="text-sm font-semibold text-danger">
            Hapus Akun Pengguna
        </h2>

        <p class="mt-0.5 text-xs text-[#6B665A]">
            Tindakan ini akan menghapus akun login Anda dari sistem secara permanen.
        </p>
    </header>

    <button type="button" 
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="btn btn-danger text-xs">
        Hapus Akun Saya
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-5">
            @csrf
            @method('delete')

            <h3 class="text-sm font-semibold text-[#1C1A15]">
                Apakah Anda yakin ingin menghapus akun ini?
            </h3>

            <p class="mt-1 text-xs text-[#6B665A]">
                Setelah akun dihapus, seluruh akses operasional dan data login akun ini akan dicabut. Masukkan kata sandi Anda untuk mengonfirmasi.
            </p>

            <div class="mt-4">
                <label for="password" class="block text-xs font-medium text-[#1C1A15] mb-1">Kata Sandi Konfirmasi</label>
                <input id="password" name="password" type="password" class="input-text text-xs" placeholder="Masukkan kata sandi akun">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
            </div>

            <div class="mt-5 flex justify-end gap-2 pt-3 border-t border-[#E2DED2]">
                <button type="button" x-on:click="$dispatch('close')" class="btn btn-secondary text-xs">
                    Batal
                </button>

                <button type="submit" class="btn btn-danger text-xs">
                    Konfirmasi Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
