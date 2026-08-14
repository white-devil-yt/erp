<?php
/*
 * cPanel one-time setup helper.
 * Place the project so that this file is reachable, e.g. https://your-domain.com/_cpanel_setup.php
 * Run it ONCE, then DELETE this file.
 * It creates the storage symlink used for uploaded files (logo etc.).
 */

echo '<pre style="font-family:Consolas;font-size:14px">';

echo "PHP: " . PHP_VERSION . "\n\n";

$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

if (! is_dir($target)) {
    echo "ERROR: target '{$target}' not found.\n";
    echo "Make sure the project is extracted fully.\n";
} elseif (file_exists($link) && is_link($link)) {
    echo "public/storage symlink already exists - nothing to do.\n";
} elseif (file_exists($link)) {
    echo "public/storage already exists as a folder/file.\n";
    echo "It was NOT created by this script. Remove it first, then re-run.\n";
} elseif (symlink($target, $link)) {
    echo "OK: symlink created:\n  {$link}  ->  {$target}\n";
} else {
    echo "symlink() is not permitted on this host.\n";
    echo "Fallback: creating real folder + copying existing uploads.\n";
    if (! is_dir($link)) {
        mkdir($link, 0775, true);
    }
    copyDirectory($target, $link);
    echo "NOTE: uploaded files will only appear here if you re-run this script later.\n";
    echo "For automatic sync, ask your host to enable symlinks or use a named folder.\n";
}

echo "\nDone. DELETE _cpanel_setup.php now.</pre>";

function copyDirectory($src, $dst): void
{
    $dir = opendir($src);
    while (($file = readdir($dir)) !== false) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $s = $src . '/' . $file;
        $d = $dst . '/' . $file;
        is_dir($s) ? copyDirectory($s, $d) : copy($s, $d);
    }
    closedir($dir);
}