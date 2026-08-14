<?php
/*
 * cPanel migration helper (for hosts WITHOUT SSH).
 * 1) First rename .env.cpanel.example -> .env and fill in your DB details.
 * 2) Visit https://your-domain.com/_cpanel_migrate.php
 * 3) DELETE this file when finished.
 * It runs Laravel's migrations + seeders (creates admin@company.com / password).
 */

header('Content-Type: text/plain; charset=utf-8');

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "DB: " . config('database.default') . ' @ ' . config('database.connections.mysql.host') . "\n\n";

try {
    $exit = $kernel->call('migrate', ['--force' => true]);
    echo "--- migrate ---\n" . $kernel->output() . "\n";
} catch (Throwable $e) {
    echo "MIGRATE ERROR: " . $e->getMessage() . "\n";
}

try {
    $kernel->call('db:seed', ['--force' => true]);
    echo "--- seed ---\n" . $kernel->output() . "\n";
} catch (Throwable $e) {
    echo "SEED ERROR: " . $e->getMessage() . "\n";
}

echo "\nDone. If you see no errors above, DELETE _cpanel_migrate.php now.\n";