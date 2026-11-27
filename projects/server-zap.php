<?php

// Local scan router. Run from projects with: php -d expose_php=0 -S 127.0.0.1:8000 -t public server-zap.php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$assets = [
    '/css/app.css' => 'text/css; charset=UTF-8',
    '/js/app.js' => 'application/javascript; charset=UTF-8',
    '/favicon.ico' => 'image/x-icon',
    '/robots.txt' => 'text/plain; charset=UTF-8',
];
if (isset($assets[$path]) && in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD'], true)) {
    header_remove('X-Powered-By');
    header('Content-Type: '.$assets[$path]);
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none'");
    header('Cache-Control: no-store, private, max-age=0');
    if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
        readfile(__DIR__.'/public'.$path);
    }
    return;
}
require __DIR__.'/public/index.php';
