<?php

declare(strict_types=1);

namespace User;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'signup' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/signup',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'create',
                    ],
                ],
            ],
            'login' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'logout' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\LogoutController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'forgot_password' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/forgot_password',
                    'defaults' => [
                        'controller' => Controller\PasswordController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'reset_password' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/reset_password[/:id[/:token]]',
                    'constraints' => [
                        'id' => '[0-9]+',
                        'token' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\PasswordController::class,
                        'action'     => 'reset_password',
                    ],
                ],
            ],
            'profile' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/profile[/:id[/:username]]',
                    'constraints' => [
                        'id' => '[0-9]+',
                        'username' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ProfileController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'settings' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/settings[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\SettingController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'admin_user' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/admin/user[/:action[/:id[/:page[/:page]]]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'id' => '[0-9]+',
                        'page' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\AuthController::class => Controller\Factory\AuthControllerFactory::class,
            Controller\LoginController::class => Controller\Factory\LoginControllerFactory::class,
            Controller\LogoutController::class => InvokableFactory::class,
            Controller\PasswordController::class => Controller\Factory\PasswordControllerFactory::class,
            Controller\ProfileController::class => InvokableFactory::class,
            Controller\SettingController::class => Controller\Factory\SettingControllerFactory::class,
            Controller\AdminController::class => Controller\Factory\AdminControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'auth/create' => __DIR__ . '/../view/user/auth/create.phtml',
            'auth/login' => __DIR__ . '/../view/user/auth/login.phtml',
            'auth/forgot_password' => __DIR__ . '/../view/user/auth/forgot_password.phtml',
            'auth/reset_password' => __DIR__ . '/../view/user/auth/reset_password.phtml',
            'profile/index' => __DIR__ . '/../view/user/profile/index.phtml',
            'setting/index' => __DIR__ . '/../view/user/setting/index.phtml',
            'setting/email' => __DIR__ . '/../view/user/setting/email.phtml',
            'setting/password' => __DIR__ . '/../view/user/setting/password.phtml',
            'setting/username' => __DIR__ . '/../view/user/setting/username.phtml',
            'admin/index' => __DIR__ . '/../view/user/admin/index.phtml',
        ],
        'template_path_stack' => [
            'user' => __DIR__ . '/../view',
        ],
    ],
];
