<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-danger disabled:opacity-50']) }}>
    {{ $slot }}
</button>
