import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    safelist: [
        // Safelist all pail-gold color variants to ensure they're included in production
        {
            pattern: /bg-pail-gold/,
            variants: ['hover', 'dark', 'group-hover'],
        },
        {
            pattern: /text-pail-gold/,
            variants: ['hover', 'dark', 'group-hover'],
        },
        {
            pattern: /border-pail-gold/,
            variants: ['hover', 'dark', 'group-hover'],
        },
        {
            pattern: /shadow-pail-gold/,
        },
        {
            pattern: /ring-pail-gold/,
        },
        // Ensure dark mode variants are included
        'dark:bg-gray-900',
        'dark:bg-gray-800',
        'dark:bg-gray-700',
        'dark:text-white',
        'dark:text-gray-400',
        'dark:text-gray-300',
        'dark:border-gray-700',
        'dark:border-gray-600',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                pail: {
                    gold: '#C9A658',
                    ivory: '#F9F9F9',
                }
            }
        },
    },

    plugins: [forms],
};
