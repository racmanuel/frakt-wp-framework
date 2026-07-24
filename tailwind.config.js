/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './public/**/*.php',
    './public/**/*.js',
  ],
  prefix: 'tkt-',
  corePlugins: {
    preflight: false,
  },
  theme: {
    extend: {
      colors: {
        wordpress: {
          50: '#f6f9e9',
          100: '#eaf3c9',
          200: '#d5e89a',
          500: '#9FCA28',
          600: '#83a91c',
          700: '#607d12',
        },
        charcoal: {
          50: '#f4f5f6',
          100: '#e4e6e8',
          200: '#c9cdd1',
          500: '#2F343A',
          600: '#252a2f',
          700: '#1d2125',
        },
      },
      boxShadow: {
        panel: '0 18px 45px rgba(47, 52, 58, 0.10)',
      },
    },
  },
};
