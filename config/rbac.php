<?php

return [
    // Keyed list of manageable modules across the app. Keep in sync with routes and sidebar labels.
    'modules' => [
        'products' => 'Products',
        'purchases' => 'Purchases',
        'stockin' => 'Stock In',
        'inventory' => 'Inventory',
        'sales' => 'Sales',
        'settings' => 'Settings',
        'user_management' => 'User Management',
    ],
    // Roles with implicit full access to all modules/actions
    'super_roles' => [
        'Admin',
    ],
];
