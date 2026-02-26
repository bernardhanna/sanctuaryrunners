<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$navigationFields = new FieldsBuilder('navigation_settings');

$navigationFields
  ->addGroup('navigation_settings_start', [
      'label' => 'Navigation Settings',
  ])
      ->addText('phone_number', [
          'label' => 'Phone Number',
          'instructions' => 'Enter the phone number to display in the header (e.g., +353 1 283 2967)',
          'placeholder' => '+353 1 283 2967',
      ])
      ->addLink('contact_button', [
          'label' => 'Get Involved Button',
          'instructions' => 'Set the "Get Involved" button link and text',
      ])
      ->addUrl('donate_link', [
          'label' => 'Donate Button Link',
          'instructions' => 'Set the URL for the donate button',
          'placeholder' => 'https://example.com/donate',
      ])
  ->addAccordion('navigation_settings_end')->endpoint();

return $navigationFields;
