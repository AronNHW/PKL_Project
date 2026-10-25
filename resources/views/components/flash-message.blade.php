@if (session('success'))
    <div class="mb-4 p-3 bg-ok-bg text-ok border border-ok/20 rounded-md text-xs flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-ok hover:opacity-75 font-bold">×</button>
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-3 bg-danger-bg text-danger border border-danger/20 rounded-md text-xs flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-danger hover:opacity-75 font-bold">×</button>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div class="mb-4 p-3 bg-danger-bg text-danger border border-danger/20 rounded-md text-xs">
        <div class="font-semibold mb-1">Periksa kembali kesalahan pengisian:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
