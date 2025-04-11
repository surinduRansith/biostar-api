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
        extend: { },
    },
    
    daisyui: {
        themes: [
          {
            mytheme: {
              
    "primary": "#ff00db",
              
    "primary-content": "#160011",
              
    "secondary": "#0078d0",
              
    "secondary-content": "#000510",
              
    "accent": "#616200",
              
    "accent-content": "#dddecf",
              
    "neutral": "#01040e",
              
    "neutral-content": "#c2c5c8",
              
    "base-100": "#e7fffc",
              
    "base-200": "#c9dedb",
              
    "base-300": "#abbebb",
              
    "base-content": "#131616",
              
    "info": "#00faff",
              
    "info-content": "#001516",
              
    "success": "#61e715",
              
    "success-content": "#031300",
              
    "warning": "#f1b800",
              
    "warning-content": "#140c00",
              
    "error": "#ff5b65",
              
    "error-content": "#160303",
              },
            },
          ],
        },

    plugins: [require('daisyui')],
};

