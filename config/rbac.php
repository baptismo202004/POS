<?php

return [
    // Keyed list of manageable modules across the app. Keep in sync with routes and sidebar labels.
    'modules' => [
        'pos' => [
            'label' => 'Point of Sale',
            'icon' => 'cash-register',
            'description' => 'Process customer transactions',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'root' => [
            'label' => 'Root',
            'permissions' => ['full'],
        ],
        'dashboard' => [
            'label' => 'Dashboard',
            'icon' => 'tachometer-alt',
            'description' => 'View sales and branch data',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'products' => [
            'label' => 'Products',
            'icon' => 'box-open',
            'description' => 'Manage products and stock',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'product_category' => [
            'label' => 'Product Category',
            'icon' => 'tags',
            'description' => 'Organize products by category',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'purchases' => [
            'label' => 'Purchase',
            'icon' => 'shopping-cart',
            'description' => 'Create and manage purchases',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'inventory' => [
            'label' => 'Inventory',
            'icon' => 'boxes',
            'description' => 'Track and manage stock levels',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'stock_in' => [
            'label' => 'Stock In',
            'icon' => 'plus-circle',
            'description' => 'Add new stock to inventory',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'stock_transfer' => [
            'label' => 'Stock Transfer',
            'icon' => 'exchange-alt',
            'description' => 'Move stock between branches',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'sales' => [
            'label' => 'Sales',
            'icon' => 'chart-line',
            'description' => 'View sales history and reports',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'sales_report' => [
            'label' => 'Sales Report',
            'icon' => 'file-invoice-dollar',
            'description' => 'Generate detailed sales reports',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'refund_return' => [
            'label' => 'Refund/Return',
            'icon' => 'undo',
            'description' => 'Process customer refunds',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'credit' => [
            'label' => 'Credit',
            'icon' => 'credit-card',
            'description' => 'Manage customer credit accounts',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'expenses' => [
            'label' => 'Expenses',
            'icon' => 'file-invoice',
            'description' => 'Track and manage expenses',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'customer' => [
            'label' => 'Customer',
            'icon' => 'users',
            'description' => 'Manage customer information',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'credit_limits' => [
            'label' => 'Credit Limits',
            'icon' => 'money-check-alt',
            'description' => 'Set and manage credit limits',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'payment_history' => [
            'label' => 'Payment History',
            'icon' => 'history',
            'description' => 'View customer payment history',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'aging_reports' => [
            'label' => 'Aging Reports',
            'icon' => 'calendar-alt',
            'description' => 'Track outstanding credit',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'reports' => [
            'label' => 'Reports',
            'icon' => 'chart-pie',
            'description' => 'Generate various reports',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'roles_permissions' => [
            'label' => 'Roles & Permissions',
            'icon' => 'user-shield',
            'description' => 'Manage user roles and access',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'user_management' => [
            'label' => 'User Management',
            'icon' => 'users-cog',
            'description' => 'Manage system users',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'access_logs' => [
            'label' => 'Access Logs',
            'icon' => 'clipboard-list',
            'description' => 'View user activity logs',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'settings' => [
            'label' => 'Settings',
            'icon' => 'cogs',
            'description' => 'Configure system settings',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'branch' => [
            'label' => 'Branch',
            'icon' => 'store',
            'description' => 'Manage store branches',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'brands' => [
            'label' => 'Brands',
            'icon' => 'copyright',
            'description' => 'Manage product brands',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'unit_types' => [
            'label' => 'Unit Types',
            'icon' => 'ruler-combined',
            'description' => 'Manage product units',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'tax' => [
            'label' => 'Tax',
            'icon' => 'percent',
            'description' => 'Manage tax rates',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
        'receipt_templates' => [
            'label' => 'Receipt Templates',
            'icon' => 'receipt',
            'description' => 'Customize receipt layouts',
            'permissions' => ['view', 'create', 'edit', 'delete'],
        ],
    ],
    'super_roles' => [
        'Admin',
    ],
];
