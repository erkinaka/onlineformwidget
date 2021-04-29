<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\OnlineformController::class,
                        'action' => 'forms',
                    ],
                ],
            ],
            'application' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'onlineform' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/onlineform[/:action][/:id]',
                    'constraints' => [
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => Controller\OnlineformController::class,
                        'action' => 'forms',
                    ],
                ],
            ],
//
            'forms' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/forms[/:page][/orderby/:orderby][/ordertype/:ordertype][/search/:search]',
                    'constraints' => [
                        'search' => '[a-zA-Z][a-zA-Z0-9_-]*\s',
                        'orderby' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'ordertype' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\OnlineformController::class,
                        'action' => 'forms',
                        'page' => 1,
                        'orderby' => 'id',
                        'ordertype' => 'asc',
                        'search' => ''
                    ]
                ],
            ],
            'add' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/add[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => Controller\OnlineformController::class,
                        'action' => 'add',
                    ],
                ],
            ],
            'edit' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/edit[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\OnlineformController::class,
                        'action' => 'edit',
                    ],
                ],
            ],
            'showform' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/showform[/:id][/:hidden_url]',
                    'constraints' => [
                        'id' => '[0-9]+',
                        'hidden_url' => '[a-zA-Z0-9_\-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\OnlineformController::class,
                        'action' => 'showform',
                    ],
                ],
            ],
            'formsave' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/formsave[/:id]',
                    'defaults' => [
                        'controller' => Controller\OnlineformController::class,
                        'action' => 'formsave',
                    ],
                ],
            ],
            'formupdate' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/formupdate[/:id]',
                    'defaults' => [
                        'controller' => Controller\OnlineformController::class,
                        'action' => 'formupdate',
                    ],
                ],
            ],
        ],
    ],

    'view_helpers' => [
        'factories' => [
            View\Helper\Breadcrumbs::class => InvokableFactory::class,
        ],
        'aliases' => [
            'pageBreadcrumbs' => View\Helper\Breadcrumbs::class,
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
