<?php

return [
    'roles_structure' => [
        'super_admin' => [
            'users' => 'c,r,u,d',
            'stores' => 'c,r,u,d',
            'categories' => 'c,r,u,d',
            'products' => 'c,r,u,d',
            'purchases' => 'c,r,u,d',
            'sales' => 'c,r,u,d',
            'clients' => 'c,r,u,d',
            'suppliers' => 'c,r,u,d',
            'expenses' => 'c,r,u,d',
            'returns' => 'c,r,u,d',
            'purchase-returns' => 'c,r,u,d',
            'safes' => 'c,r,u,d',
            'damageds' => 'c,r,u,d',
            'banks' => 'c,r,u,d',
            'employees' => 'c,r,u,d',
            'reports' => 'c,r,u,d',
        ],
        'user' => []
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];
