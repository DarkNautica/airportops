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
                sans: ['Geist', ...defaultTheme.fontFamily.sans],
                serif: ['Instrument Serif', 'Georgia', ...defaultTheme.fontFamily.serif],
                mono: ['Geist Mono', 'ui-monospace', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                sidebar: {
                    DEFAULT: '#0E1520',
                    hover: '#151F2E',
                    border: 'rgba(255,255,255,0.06)',
                },
                surface: {
                    DEFAULT: '#F5F7FA',
                    card: '#FFFFFF',
                    border: '#E4E9F0',
                },
                brand: {
                    DEFAULT: '#2563EB',
                    hover: '#3B82F6',
                    amber: '#D97706',
                    'amber-light': '#F59E0B',
                },
                status: {
                    green: '#16A34A',
                    amber: '#D97706',
                    red: '#DC2626',
                    neutral: '#64748B',
                },
            },
            boxShadow: {
                card: '0 1px 3px rgba(0,0,0,0.06)',
                modal: '0 4px 12px rgba(0,0,0,0.08)',
            },
        },
    },

    plugins: [forms],
};
