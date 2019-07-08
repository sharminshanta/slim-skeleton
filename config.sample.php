<?php
return [
    'app' => [
        'site_url' => '',
        'accounts_portal_url' => '',
        'mode' => 1,
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header,
        'error_reporting' => 1,
        'renderer' => [
            'template_path' => ROOT_DIR . DIRECTORY_SEPARATOR . 'templates/',
        ],
        'view' => [
            'template_path' => ROOT_DIR . DIRECTORY_SEPARATOR . 'templates/',
            'twig' => [
                'cache' => false,
                'debug' => true,
                'auto_reload' => true,
            ],
        ],
        'logger' => [
            'name' => 'slim-project',
            'path' => ROOT_DIR . DIRECTORY_SEPARATOR . 'logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
            'monolog_handlers' => ['php://stdout', 'file']
        ],
        'directory' => [
            'public_assets' => ROOT_DIR . DIRECTORY_SEPARATOR . 'public/assets',
            'profile_pictures' => ROOT_DIR . DIRECTORY_SEPARATOR . 'public/assets/img/profiles',
        ],
        'databases' => [
            'default' => [
                'driver' => 'mysql',
                'host' => 'localhost',
                'database' => '',
                'username' => 'e',
                'password' => '',
                'charset' => 'Utf8',
                'collation' => 'utf8_general_ci',
                'prefix' => '',
                'unix_socket' => null
            ],
        ],
        'admin_notification_email' => [
            'recipients' => [
                [
                    'name' => 'Name',
                    'email_address' => 'recipient@gmail.com'
                ],
                /*[
                    'name' => 'PREVIEW TECHNOLOGIES LIMITED',
                    'email_address' => 'info@previewtechs.com'
                ]*/
            ],
            'cc' => [
                [
                    'name' => 'Name',
                    'email_address' => 'example@gmail.com'
                ],
                [
                    'name' => 'Name',
                    'email_address' => 'example1@gmail.com'
                ]
            ],
        ],
        'portal_admin' => [
            'example@gmail.com'
        ],
        'send_email_api_endpint' => 'Your Email Endint',
        'tinyMCE_api_key' => 'tinyMCE_api_key',
        'gcaptcha' => [
            'backend_key' => 'Backend Key',
            'site_key' => 'Site Key'
        ],
    ]
];