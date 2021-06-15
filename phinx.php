<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/lib/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'sitemanager',
            'user' => 'bitrix0',
            'pass' => 'w=SwnR8l1jQh6ah@)H)7',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'sitemanager',
            'user' => 'bitrix0',
            'pass' => 'w=SwnR8l1jQh6ah@)H)7',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'testing_db',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
