<?php
// Autoload all ACF field definition files recursively.
// NOTE: PHP glob() does not reliably support ** recursion across environments.
$acf_fields_dir = get_template_directory() . '/acf-fields';

if (is_dir($acf_fields_dir)) {
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
}