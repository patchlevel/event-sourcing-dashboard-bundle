const defaultTheme = require('tailwindcss/defaultTheme');
const plugin = require('tailwindcss/plugin');

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#1166dd',
          light: 'rgb(var(--color-primary-light) / <alpha-value>)',
        },
        secondary: {
          DEFAULT: '#6341f0',
          light: 'rgb(var(--color-secondary-light) / <alpha-value>)',
        },
        tertiary: {
          DEFAULT: '#0ba6be',
          light: 'rgb(var(--color-tertiary-light) / <alpha-value>)',
        },
      },
      fontFamily: {
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
        mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms')({ strategy: 'class' }),
    plugin(({ addVariant }) => {
      addVariant('light', ['[data-theme="light"] &', '[data-theme="light"]&']);
      // Content width preference, see the switch in layout.html.twig.
      addVariant('wide', '[data-width="full"] &');
    }),
  ],
}
