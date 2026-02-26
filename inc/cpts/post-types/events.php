<?php
/**
 * Plugin Name: CPT – Events
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {

    /* CPT: event */
    register_extended_post_type(
        'event',
        [
            'menu_icon'       => 'dashicons-calendar-alt',
            'supports'        => ['title','editor','excerpt','thumbnail','revisions'],
            'public'          => true,
            'show_ui'         => true,
            'show_in_menu'    => true,
            'show_in_rest'    => true,
            'has_archive'     => true,
            'rewrite'         => ['slug' => 'events', 'with_front' => false],
            'menu_position'   => 20,
            'capability_type' => 'post',
            'map_meta_cap'    => true,
        ],
        [
            'singular' => 'Event',
            'plural'   => 'Events',
            'slug'     => 'events',
        ]
    );

    /* Taxonomy: event_location (hierarchical) */
    register_extended_taxonomy(
        'event_location',
        'event',
        [
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => ['slug' => 'event-location', 'with_front' => false],
        ],
        [
            'singular' => 'Location',
            'plural'   => 'Locations',
            'slug'     => 'event-location',
        ]
    );
});

/* Seed default location terms (runs in admin only) */
add_action('admin_init', function () {
    $ensure = function ($name, $tax) {
        if (!taxonomy_exists($tax)) return;
        if (!term_exists($name, $tax)) wp_insert_term($name, $tax);
    };

    // Edit/remove these as you like
    foreach (['Dublin','Cork','Galway','Limerick','Belfast','Online'] as $t) {
        $ensure($t, 'event_location');
    }
});

// Admin meta box for event date/time
add_action('add_meta_boxes', function () {
    add_meta_box(
        'event_date_box',
        __('Event Date', 'matrix-starter'),
        function ($post) {
            $value = get_post_meta($post->ID, 'event_start', true); // stored as Y-m-d H:i:s
            $local = $value ? date('Y-m-d\TH:i', strtotime($value)) : '';

            wp_nonce_field('save_event_date', 'event_date_nonce');

            echo '<label for="event_start" style="display:block;margin-bottom:6px;">'
               . esc_html__('Start date & time', 'matrix-starter')
               . '</label>';

            echo '<input type="datetime-local" id="event_start" name="event_start" value="'
               . esc_attr($local)
               . '" style="width:100%;max-width:320px;" />';
        },
        'event',
        'side',
        'default'
    );
});

// Save event date/time
add_action('save_post_event', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['event_date_nonce']) || !wp_verify_nonce($_POST['event_date_nonce'], 'save_event_date')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['event_start'])) {
        $raw = sanitize_text_field($_POST['event_start']); // Y-m-d\TH:i
        if ($raw) {
            $dt = DateTime::createFromFormat('Y-m-d\TH:i', $raw);
            if ($dt) {
                update_post_meta($post_id, 'event_start', $dt->format('Y-m-d H:i:s'));
            }
        } else {
            delete_post_meta($post_id, 'event_start');
        }
    }
});

// Expose meta in REST API
add_action('init', function () {
    register_post_meta('event', 'event_start', [
        'type'         => 'string',
        'single'       => true,
        'show_in_rest' => true,
        'auth_callback'=> function () { return current_user_can('edit_posts'); },
    ]);
});