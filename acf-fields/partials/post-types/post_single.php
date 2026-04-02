<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$post_single = new FieldsBuilder('post_single', [
    'label' => 'Post External Source Settings',
]);

$post_single
    ->setLocation('post_type', '==', 'post')
    ->addLink('post_external_source_link', [
        'label' => 'External Source Link',
        'instructions' => 'Optional. Add an external source URL for this post.',
        'return_format' => 'array',
    ])
    ->addTrueFalse('post_listing_open_external_source', [
        'label' => 'Open external source from blog listings',
        'instructions' => 'When enabled and an external source URL is provided, blog listing cards open the external source in a new tab.',
        'default_value' => 0,
        'ui' => 1,
    ]);

return $post_single;
