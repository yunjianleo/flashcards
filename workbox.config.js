module.exports = {
    "globDirectory": "public/",
    "globPatterns": [
        "**/*.{js,css,ico,woff2,webmanifest}",
        "**/images/icons/*",
        "**/images/*",
    ],
    manifestTransforms: [
        (originalManifest) => {
            // Add revision number to fonts - .woff2 format
            const manifest = originalManifest.map(
                (entry) => {
                    if (RegExp('.+\.(woff2)').test(entry.url)) {
                        entry.url = entry.url + '?' + entry.revision
                    }
                    return entry
                }

            );
            // Optionally, set warning messages.
            const warnings = [];
            return {
                manifest,
                warnings
            };
        }
    ],
    // 15mb max file size
    maximumFileSizeToCacheInBytes: 15 * 1024 * 1024,
    globIgnores: [
        '**/mix-manifest.json',
        '**/vendor/telescope/*',
        '**/js/{manifest,vendor}.js',
        '**/js/chunks/*',
    ],
    "swDest": "public/service-worker.js",
    "swSrc": "resources/js/config/service-worker.js"
};
