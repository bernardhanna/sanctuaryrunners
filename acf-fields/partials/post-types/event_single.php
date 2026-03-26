<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$event_single = new FieldsBuilder('event_single', [
    'label' => 'Event Single Settings',
]);

$event_single
    ->setLocation('post_type', '==', 'event')
    ->addLink('event_registration_link', [
        'label' => 'Event Registration Link',
        'instructions' => 'Optional button shown at the end of event posts (e.g. external registration form).',
        'return_format' => 'array',
    ])
    ->addText('event_registration_label', [
        'label' => 'Event Registration Button Text',
        'instructions' => 'Optional. Leave blank to use "Register for event".',
    ]);

return $event_single;
