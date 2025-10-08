import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/components/admin/**/*.blade.php',
        './resources/views/admin/**/*.blade.php',
        './resources/views/vendor/pagination/**/*.blade.php',
        './app/Forms/Admin/*.php',
        './config/form-builder.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Colori custom del progetto
                'custom-primary': {
                    DEFAULT: '#F97316', // Orange-500
                    foreground: '#FFFFFF',
                },
                'custom-secondary': {
                    DEFAULT: '#FACC15', // Yellow-400
                    foreground: '#374151', // Gray-700
                },
            },
        },
    },

    plugins: [
        forms,
        require('daisyui')
    ],

    daisyui: {
        themes: [
            {
                light: {
                    ...require("daisyui/src/theming/themes")["light"],
                    primary: "#F97316",
                    "primary-content": "#FFFFFF",
                    secondary: "#FACC15",
                    "secondary-content": "#374151",
                    accent: "#06B6D4", // Cyan-500
                    neutral: "#1F2937", // Gray-800
                    "base-100": "#FFFFFF",
                    "base-200": "#F3F4F6", // Gray-100
                    "base-300": "#E5E7EB", // Gray-200
                    info: "#3B82F6", // Blue-500
                    success: "#10B981", // Green-500
                    warning: "#F59E0B", // Amber-500
                    error: "#EF4444", // Red-500
                },
                dark: {
                    ...require("daisyui/src/theming/themes")["dark"],
                    primary: "#F97316",
                    "primary-content": "#FFFFFF",
                    secondary: "#FACC15",
                    "secondary-content": "#374151",
                    accent: "#06B6D4",
                    neutral: "#374151",
                    "base-100": "#1F2937", // Gray-800
                    "base-200": "#111827", // Gray-900
                    "base-300": "#0F172A", // Slate-900
                    info: "#3B82F6",
                    success: "#10B981",
                    warning: "#F59E0B",
                    error: "#EF4444",
                },
            },
        ],
        darkTheme: "dark",
        base: true,
        styled: true,
        utils: true,
        logs: false,
    },
};
