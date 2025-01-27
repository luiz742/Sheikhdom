import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                oficial: {
                    DEFAULT: 'rgb(249 115 22 / var(--tw-bg-opacity, 1))', // Cor principal
                    hover: 'rgb(249 115 22 / var(--tw-bg-opacity, 1))',  // Cor ao passar o mouse
                },                
            },
        },
    },

    // Adiciona suporte ao modo escuro com base na classe `dark`
    darkMode: 'class',

    plugins: [forms, typography],
};
