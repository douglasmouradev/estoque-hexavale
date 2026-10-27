/** @type {import('tailwindcss').Config} */
export default {
  content: ['./resources/prototipo/**/*.html'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'Source Sans 3', 'sans-serif'],
      },
      colors: {
        brand: {
          50: '#f0fdf4',
          100: '#dcfce7',
          200: '#bbf7d0',
          300: '#86efac',
          400: '#4ade80',
          500: '#22c55e',
          600: '#16a34a',
          700: '#15803d',
          800: '#166534',
          900: '#14532d',
          950: '#052e16',
        },
      },
    },
  },
  safelist: [
    { pattern: /^(bg|text|border|ring|from|to|via)-brand-(50|100|200|300|400|500|600|700|800|900|950)(\/\d+)?$/ },
  ],
  plugins: [],
};
