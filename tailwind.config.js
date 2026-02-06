import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,jsx,ts,tsx}',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                soft: '0 20px 60px -40px rgba(15, 23, 42, 0.6)',
                glow: '0 0 0 1px rgba(16, 185, 129, 0.2), 0 20px 60px -40px rgba(16, 185, 129, 0.8)',
            },
            borderRadius: {
                '3xl': '1.5rem',
                '4xl': '2rem',
            },
        },
    },

    plugins: [forms],
};
