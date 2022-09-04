const colors = require('tailwindcss/colors')
const forms = require('@tailwindcss/forms')

module.exports = {
  mode: 'jit',
  content: [
    './resources/views/**/*.blade.php',
    './resources/vue/**/*.vue',
  ],
  theme: {
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      black: colors.black,
      white: colors.white,
      gray: {
        DEFAULT: colors.neutral[500],
        ...colors.neutral
      },
      red: {
        DEFAULT: colors.red[500],
        ...colors.red
      },
      yellow: {
        DEFAULT: colors.amber[500],
        ...colors.amber
      },
      green: {
        DEFAULT: colors.emerald[500],
        ...colors.emerald
      },
    },
    fontFamily: {
      sans: ['Oxygen', 'sans-serif']
    },
  },
  plugins: [
    forms
  ]
}
