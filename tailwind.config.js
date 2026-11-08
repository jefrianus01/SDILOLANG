import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                // Identitas sekolah: teal-navy yang segar, emas hangat, dan kertas putih bersih.
                ink: {
                    DEFAULT: '#0B2E3A',
                    950: '#051E27',
                    900: '#082731',
                    800: '#0C3542',
                    700: '#114555',
                    600: '#165A6F',
                    500: '#1D738C',
                },
                wine: {
                    DEFAULT: '#B23A52',
                    700: '#963046',
                    600: '#C24A63',
                    500: '#D45F78',
                },
                gold: {
                    DEFAULT: '#F0B43C',
                    light: '#F8D98A',
                    dark: '#8A5E0C',
                },
                parchment: '#EFF8F7',
                ivory: '#FBFEFD',
                stone: '#D8EAE7',
                // Warna aksi utama: biru-teal cerah yang tetap profesional.
                brand: {
                    50: '#EDF9FB',
                    100: '#D3F1F6',
                    200: '#A9E3ED',
                    300: '#72CEDF',
                    400: '#39B0C8',
                    500: '#1A93AD',
                    600: '#127690',
                    700: '#126078',
                    800: '#144E62',
                    900: '#144253',
                    950: '#082935',
                },
                // Netral sejuk agar senada dengan ivory/parchment yang lebih segar.
                gray: {
                    50: '#F7FAFA',
                    100: '#EEF4F4',
                    200: '#DEE9E9',
                    300: '#C5D6D7',
                    400: '#7A9194',
                    500: '#5C777A',
                    600: '#486266',
                    700: '#374E51',
                    800: '#24383B',
                    900: '#162629',
                    950: '#0C181A',
                },
            },
            fontFamily: {
                sans: ['Manrope', 'Figtree', ...defaultTheme.fontFamily.sans],
                display: ['"Cormorant Garamond"', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
            fontSize: {
                '2xs': ['0.6875rem', { lineHeight: '1rem' }],
            },
            boxShadow: {
                card: '0 1px 2px rgba(28, 25, 23, 0.04), 0 1px 3px rgba(28, 25, 23, 0.05)',
                float: '0 16px 40px -18px rgba(28, 25, 23, 0.28)',
                soft: '0 30px 80px -40px rgba(11, 46, 58, 0.45)',
                gold: '0 18px 40px -20px rgba(240, 180, 60, 0.45)',
            },
            letterSpacing: {
                ultra: '0.32em',
            },
        },
    },

    plugins: [forms],
};
