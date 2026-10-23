<?php

// 1. Prepare writable /tmp directories for Vercel Serverless environment
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 2. Set environment variables for serverless execution
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');

// 3. Prepare SQLite database in writable /tmp
$srcDb = __DIR__ . '/../database/database.sqlite';
$tmpDb = '/tmp/database.sqlite';

if (!file_exists($tmpDb)) {
    if (file_exists($srcDb)) {
        copy($srcDb, $tmpDb);
    } else {
        touch($tmpDb);
    }
}

putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=' . $tmpDb);
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $tmpDb;

// 4. Ensure production APP_KEY and safe session driver
if (!getenv('APP_KEY')) {
    putenv('APP_KEY=base64:rKpVvrSPXrcXUONoGzw4akAWiguBBuhXgykUDBJLkgw=');
    $_ENV['APP_KEY'] = 'base64:rKpVvrSPXrcXUONoGzw4akAWiguBBuhXgykUDBJLkgw=';
}

putenv('SESSION_DRIVER=cookie');
$_ENV['SESSION_DRIVER'] = 'cookie';

// 5. Force HTTPS for Vercel Serverless environment to prevent Mixed Content blocking
$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = 443;
putenv('APP_URL=https://' . ($_SERVER['HTTP_HOST'] ?? 'naivas-command-center.vercel.app'));

// 6. Run Laravel front controller
require __DIR__ . '/../public/index.php';
