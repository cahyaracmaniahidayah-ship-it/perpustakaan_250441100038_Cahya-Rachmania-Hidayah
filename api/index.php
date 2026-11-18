<?php

// Di Vercel, filesystem proyek read-only. Hanya /tmp yang bisa ditulis.
$tmp = '/tmp';

foreach ([
    "$tmp/storage/framework/views",
    "$tmp/storage/framework/cache/data",
    "$tmp/storage/framework/sessions",
    "$tmp/storage/logs",
    "$tmp/bootstrap/cache",
] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Arahkan cache Laravel ke /tmp agar tidak error "not writable"
$overrides = [
    'VIEW_COMPILED_PATH' => "$tmp/storage/framework/views",
    'APP_SERVICES_CACHE' => "$tmp/bootstrap/cache/services.php",
    'APP_PACKAGES_CACHE' => "$tmp/bootstrap/cache/packages.php",
    'APP_CONFIG_CACHE'   => "$tmp/bootstrap/cache/config.php",
    'APP_ROUTES_CACHE'   => "$tmp/bootstrap/cache/routes.php",
    'APP_EVENTS_CACHE'   => "$tmp/bootstrap/cache/events.php",
    'LOG_CHANNEL'        => 'stderr',
];

foreach ($overrides as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

// Teruskan ke pintu masuk Laravel yang asli
require __DIR__ . '/../public/index.php';