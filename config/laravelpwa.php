<?php

return [
    // Package top-level name (optional label)
    'name' => 'My App',

    // REQUIRED: manifest block
    'manifest' => [
        'name' => 'My App',
        'short_name' => 'App',
        'start_url' => '/',
        'background_color' => '#ffffff',
        'theme_color' => '#ffffff',
        'display' => 'standalone',
        'orientation' => 'portrait',
        'status_bar' => 'black',

        // IMPORTANT: include 192 and 512 sizes (Chrome install requirement)
        'icons' => [
            '72x72'   => ['path' => '/images/icons/icon-72x72.png',   'purpose' => 'any'],
            '96x96'   => ['path' => '/images/icons/icon-96x96.png',   'purpose' => 'any'],
            '128x128' => ['path' => '/images/icons/icon-128x128.png', 'purpose' => 'any'],
            '144x144' => ['path' => '/images/icons/icon-144x144.png', 'purpose' => 'any'],
            '152x152' => ['path' => '/images/icons/icon-152x152.png', 'purpose' => 'any'],
            '192x192' => ['path' => '/images/icons/icon-192x192.png', 'purpose' => 'any'], // required
            '384x384' => ['path' => '/images/icons/icon-384x384.png', 'purpose' => 'any'],
            '512x512' => ['path' => '/images/icons/icon-512x512.png', 'purpose' => 'any'], // required
        ],

        // optional extras:
       'splash' => [
    '640x1136'  => '/images/icons/splash-640x1136.png',
    '750x1334'  => '/images/icons/splash-750x1334.png',
    '828x1792'  => '/images/icons/splash-828x1792.png',
    '1125x2436' => '/images/icons/splash-1125x2436.png',
    '1242x2208' => '/images/icons/splash-1242x2208.png',
    '1242x2688' => '/images/icons/splash-1242x2688.png',
    '1536x2048' => '/images/icons/splash-1536x2048.png',
    '1668x2224' => '/images/icons/splash-1668x2224.png',
    '1668x2388' => '/images/icons/splash-1668x2388.png',
    '2048x2732' => '/images/icons/splash-2048x2732.png',
],

        'shortcuts'=> [],
        'custom'   => [],
    ],

    // Keep the service worker enabled
    'serviceworker' => [
        'enable' => true,
        'path'   => public_path('serviceworker.js'),
],
];