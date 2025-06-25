/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: 'gfr-', // Prefix all classes to avoid conflicts
  important: '#gitflow-reporter-widget', // Make our styles more specific
  content: [
    './resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}