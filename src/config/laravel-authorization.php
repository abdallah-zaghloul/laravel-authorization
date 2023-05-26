<?php

/**
 * you should type moduled permissions as the following example:
 * guard => [module => [...permissions] ]
 * each guard should be existed at your config(auth.guards)
 * once you publish this config file ... note that if you put invalid guards,modules,permission names your will see an error before you boot your app
 *
    'api' => [
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
 */
return [

    /* super role ... the first created role by which the app will start */
    'superRoleGuard' => 'api',
    'firstSuperRoleGuardPrimaryKey' => '1',

    /* default pagination count during roles index */
    'paginationCount' => 50,

    'moduled_permissions'=> [
        'api'=> [
            'categories' => [
                'create',
                'read',
                'update',
                'delete',
            ],
            'products' => [
                'create',
                'read',
                'update',
                'delete',
            ],
        ],
    ],
];
