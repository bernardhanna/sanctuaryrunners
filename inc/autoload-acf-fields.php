<?php
$acf_fields_dir = get_template_directory() . '/acf-fields';

if (!is_dir($acf_fields_dir)) {
    return;
}

// Keep legacy single-site loading behavior, but use robust recursive loading in multisite.
if (!is_multisite()) {
    foreach (glob($acf_fields_dir . '/**/*.php') as $file) {
        require_once $file;
    }
    return;
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($acf_fields_dir, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file->isFile()) {
        continue;
    }
    if (substr($file->getFilename(), -4) !== '.php') {
        continue;
    }
    require_once $file->getPathname();
}