import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Instrument Sans"', ...defaultTheme.fontFamily.sans],
                display: ['"Bricolage Grotesque"', '"Instrument Sans"', ...defaultTheme.fontFamily.sans],
                // Kode barang dan angka tidak lagi monospace; angka sejajar lewat tabular-nums.
                mono: ['"Instrument Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                accent: {
                    DEFAULT: '#0B6B73',
                    hover: '#08565D',
                    light: '#E3F0F0',
                },
                ink: '#1C1A15',
                frame: '#E6E2D7',
                sheet: '#FBFAF6',
                ok: {
                    DEFAULT: '#3E7B22',
                    bg: '#EAF2E4',
                },
                warn: {
                    DEFAULT: '#9A5B00',
                    bg: '#FBF0DC',
                },
                danger: {
                    DEFAULT: '#B42318',
                    bg: '#FCEAE8',
                },
                surface: '#FFFFFF',
                canvas: '#EEEBE3',
                border: '#E2DED2',
                'text-main': '#1C1A15',
                'text-muted': '#6B665A',
            },
            borderRadius: {
                DEFAULT: '8px',
                sm: '6px',
                md: '8px',
                lg: '10px',
            },
        },
    },

    plugins: [forms],
};
