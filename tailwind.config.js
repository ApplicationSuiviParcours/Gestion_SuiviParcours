import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
    './resources/views/**/*.blade.php',
    './resources/views/filament/**/*.blade.php', // ajoute si tu as tes views ici
    './vendor/filament/**/*.blade.php',
    './app/Filament/**/*.php',
    "./resources/**/*.js",
    "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
