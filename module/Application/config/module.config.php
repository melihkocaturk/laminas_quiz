<?php

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'help' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/help[/:action]',
                    'constraints' => [
                        'action' => '(contact|privacy|terms)',
                    ],
                    'defaults' => [
                        'controller' => Controller\HelpController::class,
                    ],
                ],
            ],
            'quiz' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/quiz[/:action[/:id[/:slug]]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'id' => '[0-9]+',
                        'slug' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\QuizController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
            Controller\HelpController::class => InvokableFactory::class,
            Controller\QuizController::class => Controller\Factory\QuizControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'help/contact' => __DIR__ . '/../view/application/help/contact.phtml',
            'help/privacy' => __DIR__ . '/../view/application/help/privacy.phtml',
            'help/terms' => __DIR__ . '/../view/application/help/terms.phtml',
            'help/delete' => __DIR__ . '/../view/application/help/delete.phtml',
            'quiz/index' => __DIR__ . '/../view/application/quiz/index.phtml',
        ],
        'template_path_stack' => [
            'application' => __DIR__ . '/../view',
        ],
    ],
];
