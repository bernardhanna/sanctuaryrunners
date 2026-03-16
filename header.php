<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <style>[x-cloak]{ display:none !important; }</style>

</head>
<body <?php body_class(); ?>>

    <?php wp_body_open(); ?>
    <header class="relative">
        <?php
        if ( function_exists( 'matrix_donations_is_donation_flow' ) && matrix_donations_is_donation_flow() ) {
            do_action( 'matrix_donations_header' );
        } else {
            get_template_part( 'template-parts/header/navbar' );
        }
        ?>
    </header>