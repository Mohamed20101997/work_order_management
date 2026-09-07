<?php

// Central role => permissions map. Admin implicitly has every permission
// (see App\Models\User::hasPermission).
return [
    'roles' => [
        'admin' => ['*'],
        'manager' => [
            'companies.view', 'companies.manage',
            'assets.view', 'assets.create', 'assets.update', 'assets.delete', 'assets.status',
            'work-orders.view', 'work-orders.create', 'work-orders.update',
            'work-orders.assign', 'work-orders.status', 'work-orders.notes',
            'inspections.view', 'inspections.manage',
            'parts.view', 'parts.manage', 'parts.use',
            'attachments.upload',
            'reports.view',
        ],
        'technician' => [
            'companies.view',
            'assets.view',
            'work-orders.view', 'work-orders.status', 'work-orders.notes',
            'inspections.view', 'inspections.manage',
            'parts.view', 'parts.use',
            'attachments.upload',
        ],
        'viewer' => [
            'companies.view',
            'assets.view',
            'work-orders.view',
            'inspections.view',
            'parts.view',
            'reports.view',
        ],
    ],
];
