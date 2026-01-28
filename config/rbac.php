<?php

return [
    // Keyed list of manageable modules across the app. Keep in sync with routes and sidebar labels.
    'modules' => [
        'root' => [
            'label' => 'Root',
            'permissions' => ['full'],
        ],
        'dashboard' => [
            'label' => 'Dashboard',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'products' => [
            'label' => 'Products',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'product_category' => [
            'label' => 'Product Category',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'purchases' => [
            'label' => 'Purchase',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'inventory' => [
            'label' => 'Inventory',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'stock_in' => [
            'label' => 'Stock In',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'stock_transfer' => [
            'label' => 'Stock Transfer',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'sales' => [
            'label' => 'Sales',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'sales_report' => [
            'label' => 'Sales Report',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'refund_return' => [
            'label' => 'Refund/Return',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'credit' => [
            'label' => 'Credit',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'expenses' => [
            'label' => 'Expenses',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'customer' => [
            'label' => 'Customer',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'credit_limits' => [
            'label' => 'Credit Limits',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'payment_history' => [
            'label' => 'Payment History',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'aging_reports' => [
            'label' => 'Aging Reports',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'reports' => [
            'label' => 'Reports',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'roles_permissions' => [
            'label' => 'Roles & Permissions',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'user_management' => [
            'label' => 'User Management',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'access_logs' => [
            'label' => 'Access Logs',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'settings' => [
            'label' => 'Settings',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'branch' => [
            'label' => 'Branch',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'brands' => [
            'label' => 'Brands',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'unit_types' => [
            'label' => 'Unit Types',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'tax' => [
            'label' => 'Tax',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'receipt_templates' => [
            'label' => 'Receipt Templates',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
    ],
    'super_roles' => [
        'Admin',
    ],
];
