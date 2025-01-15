// browser-sync-config.js
module.exports = {
    files: [
        './**/*.php',
        './**/*.html',
        './**/*.css',
        './**/*.js'
    ],
    proxy: "localhost:3000", // Update this to match your PHP Server port
    port: 3001,
    open: false,
    notify: false
};