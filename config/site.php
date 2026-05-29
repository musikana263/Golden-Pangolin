<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Golden Pangolin — Site Settings
    |--------------------------------------------------------------------------
    | Brand-wide values used across views (WhatsApp float, footer, share links).
    | Override in .env where helpful.
    */

    'brand' => env('SITE_BRAND', 'Golden Pangolin'),

    'whatsapp' => [
        'number' => env('SITE_WHATSAPP_NUMBER', '26371527381'),
        'default_message' => env(
            'SITE_WHATSAPP_DEFAULT_MESSAGE',
            "Hello Golden Pangolin! I'd like to learn more about your tours."
        ),
    ],

    // Southern African countries offered as a destination country in the admin
    // form. Alphabetical so admins can scan the list quickly. Covers the
    // mainland SADC tourism circuit — the offshore island states (Comoros,
    // Madagascar, Mauritius, Seychelles) and DRC are intentionally omitted
    // because Golden Pangolin doesn't run trips there.
    'countries' => [
        'Angola',
        'Botswana',
        'Eswatini',
        'Kenya',
        'Lesotho',
        'Malawi',
        'Mozambique',
        'Namibia',
        'South Africa',
        'Tanzania',
        'Zambia',
        'Zimbabwe',
    ],

    /*
    | Accommodation amenities — value => [label, icon]
    | Icons are Font Awesome classes.
    */
    'amenities' => [
        'wifi'         => ['label' => 'Free Wi-Fi',          'icon' => 'fas fa-wifi'],
        'pool'         => ['label' => 'Swimming Pool',       'icon' => 'fas fa-swimming-pool'],
        'breakfast'    => ['label' => 'Breakfast Included',  'icon' => 'fas fa-mug-hot'],
        'restaurant'   => ['label' => 'On-site Restaurant',  'icon' => 'fas fa-utensils'],
        'bar'          => ['label' => 'Bar / Lounge',        'icon' => 'fas fa-cocktail'],
        'spa'          => ['label' => 'Spa & Wellness',      'icon' => 'fas fa-spa'],
        'gym'          => ['label' => 'Fitness Centre',      'icon' => 'fas fa-dumbbell'],
        'parking'      => ['label' => 'Free Parking',        'icon' => 'fas fa-parking'],
        'airport'      => ['label' => 'Airport Transfer',    'icon' => 'fas fa-plane-arrival'],
        'pet'          => ['label' => 'Pet Friendly',        'icon' => 'fas fa-paw'],
        'air_con'      => ['label' => 'Air Conditioning',    'icon' => 'fas fa-snowflake'],
        'fireplace'    => ['label' => 'Fireplace',           'icon' => 'fas fa-fire'],
        'fan'          => ['label' => 'Ceiling Fan',         'icon' => 'fas fa-fan'],
        'family'       => ['label' => 'Family Friendly',     'icon' => 'fas fa-users'],
        'kitchen'      => ['label' => 'Self-Catering Kitchen', 'icon' => 'fas fa-blender'],
        'laundry'      => ['label' => 'Laundry Service',     'icon' => 'fas fa-tshirt'],
        'safari'       => ['label' => 'Game Drives Included', 'icon' => 'fas fa-binoculars'],
        'guided'       => ['label' => 'Guided Walks',        'icon' => 'fas fa-hiking'],
        'river_view'   => ['label' => 'River View',          'icon' => 'fas fa-water'],
        'bush_view'    => ['label' => 'Bush / Wildlife View', 'icon' => 'fas fa-tree'],
        'tea_coffee'   => ['label' => 'Tea & Coffee',        'icon' => 'fas fa-coffee'],
        'tv'           => ['label' => 'TV',                  'icon' => 'fas fa-tv'],
        'safe'         => ['label' => 'In-Room Safe',        'icon' => 'fas fa-lock'],
        'kids_pool'    => ['label' => "Children's Pool",     'icon' => 'fas fa-child'],
        'wheelchair'   => ['label' => 'Wheelchair Accessible', 'icon' => 'fas fa-wheelchair'],
        'eco'          => ['label' => 'Eco-Friendly',        'icon' => 'fas fa-leaf'],
    ],

];
