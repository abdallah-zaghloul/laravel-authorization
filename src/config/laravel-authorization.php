<?php

/**
 * you should type moduled permissions as the following example:
 * guard_name => [module_name => [...permissions] ]
 *
    'admin' => [
               'categories' => [
                                  'create',
                                  'read',
                                  'update',
                                  'delete',
                               ],
               'products' =>   [
                                  'create',
                                  'read',
                                  'update',
                                  'delete',
                               ],
    ],
    'user' => [
               'categories' => [
                                  'read',
                               ],
               'products' =>   [
                                  'read',
                               ],
    ],
 */
return [

    /* super role ... the first created role by which the app will start */
    'superRoleName' => 'Super',
    'superRoleGuard' => 'admin',

    /* default pagination count during roles index */
    'paginationCount' => 50,

    'moduled_permissions'=> [
        'user'=> [
            'categories' => [
                'read',
            ],
            'products' => [
                'read',
            ],
        ],
        'admin'=> [
            'categories' => [
                'create',
                'read',
                'update',
                'delete'
            ],
            'products' => [
                'create',
                'read',
                'update',
                'delete'
            ],
        ]
    ],
];
