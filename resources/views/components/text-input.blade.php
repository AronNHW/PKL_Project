@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'input-text disabled:bg-gray-100 disabled:cursor-not-allowed']) }}>
