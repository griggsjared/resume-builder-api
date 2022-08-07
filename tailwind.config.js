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
      gray: colors.neutral
    },
    fontFamily: {
      sans: ['Oxygen', 'sans-serif']
    },
  },
  plugins: [
    forms
  ]
}
