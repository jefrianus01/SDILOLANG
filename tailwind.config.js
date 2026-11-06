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
                // Identitas sekolah: navy tinta, emas, dan kertas ivory.
                ink: {
                    DEFAULT: '#0A1F33',
                    950: '#061624',
                    900: '#081A2B',
                    800: '#0F2A44',
                    700: '#163A5A',
                    600: '#1F4E73',
                    500: '#2B648F',
                },
                wine: {
                    DEFAULT: '#6E1D2A',
                    700: '#5A1622',
                    600: '#812536',
                    500: '#983044',
                },
                gold: {
                    DEFAULT: '#C6A15B',
                    light: '#E7D3A8',
                    dark: '#A47E3C',
                },
                parchment: '#F6F1E7',
                ivory: '#FCFAF6',
                stone: '#E8E2D7',
                // Warna aksi utama (turunan ink, cukup kontras untuk teks putih).
                brand: {
                    50: '#F2F6F9',
                    100: '#E3ECF2',
                    200: '#C8D9E5',
                    300: '#A1BDD1',
                    400: '#6E94AE',
                    500: '#4A7290',
                    600: '#355A76',
                    700: '#294861',
                    800: '#1E374B',
                    900: '#152838',
                    950: '#0B1A26',
                },
                // Netral hangat agar senada dengan ivory/parchment (bukan abu dingin).
                gray: {
                    50: '#FAF8F5',
                    100: '#F3F0EA',
                    200: '#E6E1D8',
                    300: '#D3CCC1',
                    400: '#A69D91',
                    500: '#786F64',
                    600: '#565049',
                    700: '#403B35',
                    800: '#2A2723',
                    900: '#1A1815',
                    950: '#100F0D',
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
                soft: '0 30px 80px -40px rgba(10, 31, 51, 0.45)',
                gold: '0 18px 40px -20px rgba(198, 161, 91, 0.45)',
            },
            letterSpacing: {
                ultra: '0.32em',
            },
        },
    },

    plugins: [forms],
};
