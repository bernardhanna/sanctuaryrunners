<?php
$acf_fields_dir = get_template_directory() . '/acf-fields';

if (!is_dir($acf_fields_dir)) {
    return;
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($acf_fields_dir, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file->isFile() || substr($file->getFilename(), -4) !== '.php') {
        continue;
    }

    $path = $file->getPathname();

    // Flexible block partials are loaded by `acf-fields/partials/flexi.php`.
    // Loading them here first causes require_once collisions and empty layouts.
    if (strpos($path, '/acf-fields/partials/blocks/') !== false) {
        continue;
    }

    require_once $path;
}