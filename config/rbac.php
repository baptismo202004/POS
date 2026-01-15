<?php

return [
    // Keyed list of manageable modules across the app. Keep in sync with routes and sidebar labels.
    'modules' => [
        'root' => [
            'label' => 'Root',
            'permissions' => ['full'],
        ],
        'administrator' => [
            'label' => 'Administrator',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'customer' => [
            'label' => 'Customer',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'expense' => [
            'label' => 'Expense',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'item' => [
            'label' => 'Item',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'lead' => [
            'label' => 'Lead',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'report' => [
            'label' => 'Report',
            'permissions' => ['view'],
        ],
        'setting' => [
            'label' => 'Setting',
            'permissions' => ['view', 'edit'],
        ],
    ],
    // Roles with implicit full access to all modules/actions
    'super_roles' => [
        'Admin',
    ],
];
