<?php

return [
    // Keyed list of manageable modules across the app. Keep in sync with routes and sidebar labels.
    'modules' => [
        'root' => [
            'label' => 'Root',
            'permissions' => ['full'],
        ],
        'products' => [
            'label' => 'Products',
            'permissions' => ['view', 'edit', 'full'],
        ],
        'purchases' => [
            'label' => 'Purchases',
            'permissions' => ['view', 'edit', 'full'],
        ],
        'stockin' => [
            'label' => 'Stock In',
            'permissions' => ['view', 'edit', 'full'],
        ],
        'inventory' => [
            'label' => 'Inventory',
            'permissions' => ['view', 'edit', 'full'],
        ],
        'sales' => [
            'label' => 'Sales',
            'permissions' => ['view', 'edit', 'full'],
        ],
        'user_management' => [
            'label' => 'User Management',
            'permissions' => ['view', 'edit', 'full'],
        ],
    ],
    'super_roles' => [
        'Admin',
    ],
];
