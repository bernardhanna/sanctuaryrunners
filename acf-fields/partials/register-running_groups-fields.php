<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

// Safety: only run when ACF is active.
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // Load all partials from /acf-fields/partials/running_groups/*.php (or a single team.php)
    $partials_dir = get_template_directory() . '/acf-fields/partials/running_groups';
    $files = [];

    if (is_dir($partials_dir)) {
        $files = glob($partials_dir . '/*.php') ?: [];
    }

    // If you keep a single file at /acf-fields/partials/running_groups.php, load that too
    $single_partial = get_template_directory() . '/acf-fields/partials/running_groups.php';
    if (file_exists($single_partial)) {
        $files[] = $single_partial;
    }

    if (empty($files)) {
        error_log('[ACF] No team field partials found.');
        return;
    }

    foreach ($files as $file) {
        $group = require $file;

        if ($group instanceof FieldsBuilder) {
            // IMPORTANT: Make sure the partial sets ->setLocation('post_type', '==', 'team')
            acf_add_local_field_group($group->build());
        } else {
            error_log("[ACF] Invalid FieldsBuilder returned from: {$file}");
        }
    }
});
