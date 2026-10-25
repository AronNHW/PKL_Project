<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-secondary disabled:opacity-50']) }}>
    {{ $slot }}
</button>
