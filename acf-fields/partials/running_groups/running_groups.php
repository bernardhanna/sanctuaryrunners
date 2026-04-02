<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$running_groups = new FieldsBuilder('running_groups', [
    'label' => 'Running Group Details',
]);

$running_groups->setLocation('post_type', '==', 'running_group');

$running_groups
    ->addTab('Location', ['label' => 'Location'])
    ->addText('address', [
        'label' => 'Address',
        'instructions' => 'Enter the full address where the group meets.',
    ])
    ->addNumber('latitude', [
        'label' => 'Latitude',
        'instructions' => 'Enter the latitude coordinate for the meeting location.',
        'step' => 0.000001,
    ])
    ->addNumber('longitude', [
        'label' => 'Longitude',
        'instructions' => 'Enter the longitude coordinate for the meeting location.',
        'step' => 0.000001,
    ])
    ->addTab('Meeting Details', ['label' => 'Meeting Details'])
    ->addText('meeting_time', [
        'label' => 'Meeting Time',
        'instructions' => 'Enter when the group meets (e.g., "Saturdays at 9:00 AM").',
    ])
    ->addWysiwyg('meeting_description', [
        'label' => 'Meeting Description',
        'instructions' => 'Describe what happens during group meetings.',
        'media_upload' => 0,
        'tabs' => 'all',
        'toolbar' => 'full',
    ])
    ->addSelect('activity_level', [
        'label' => 'Activity Level',
        'instructions' => 'Select the primary activity level for this group.',
        'choices' => [
            'walking' => 'Walking',
            'jogging' => 'Jogging',
            'running' => 'Running',
            'mixed' => 'Mixed Activities',
        ],
        'default_value' => 'mixed',
    ])
    ->addTab('Contact', ['label' => 'Contact'])
    ->addText('contact_name', [
        'label' => 'Contact Name',
        'instructions' => 'Name of the group leader or contact person.',
    ])
    ->addEmail('contact_email', [
        'label' => 'Contact Email',
        'instructions' => 'Email address for group inquiries.',
    ])
    ->addText('contact_phone', [
        'label' => 'Contact Phone',
        'instructions' => 'Phone number for group inquiries.',
    ])
    ->addText('contact_info', [
        'label' => 'Additional Contact Info',
        'instructions' => 'Any additional contact information or social media links.',
    ])
    ->addTab('Group Details', ['label' => 'Group Details'])
    ->addNumber('group_size', [
        'label' => 'Typical Group Size',
        'instructions' => 'Average number of participants.',
        'min' => 1,
    ])
    ->addTrueFalse('show_map_popup_link', [
        'label' => 'Show "View group" link in map popups',
        'instructions' => 'Enable this only if this running group should show a popup link on maps.',
        'default_value' => 0,
        'ui' => 1,
    ])
    ->addTrueFalse('beginner_friendly', [
        'label' => 'Beginner Friendly',
        'instructions' => 'Is this group suitable for beginners?',
        'default_value' => 1,
    ])
    ->addTrueFalse('wheelchair_accessible', [
        'label' => 'Wheelchair Accessible',
        'instructions' => 'Is the meeting location wheelchair accessible?',
        'default_value' => 0,
    ])
    ->addTextarea('special_notes', [
        'label' => 'Special Notes',
        'instructions' => 'Any special requirements, equipment needed, or other important information.',
        'rows' => 3,
    ]);

// Set location for the field group
$running_groups->setLocation('post_type', '==', 'running_group');

return $running_groups;
