<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'admin-home' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin[/]',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),
            'admin-login' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/login[/:action]',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Login',
                        'action' => 'index',
                    ),
                ),
            ),
            'admin-role' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/role[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Role',
                        'action' => 'index',
                    ),
                ),
            ),
            'admin-user' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/user[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action' => 'index',
                    ),
                ),
            ),
            'admin-customer' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/customer[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Customer',
                        'action' => 'index',
                    ),
                ),
            ),
            'admin-vehicle-brands' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/vehicle-brands[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\VehicleBrands',
                        'action' => 'index',
                    ),
                ),
            ),
            'admin-zone' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/zone[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Zone',
                        'action' => 'index',
                    ),
                ),
            ),
            'admin-product' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/product[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Product',
                        'action' => 'index',
                    ),
                ),
            ),
            'admin-qty' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/qty[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\QtyDetails',
                        'action' => 'index',
                    ),
                ),
            ),
            'admin-supplier' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/supplier[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Supplier',
                        'action' => 'index',
                    ),
                ),
            ),
            'admin-purchase' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/purchase[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Purchase',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Admin\Controller\Index'            => 'Admin\Controller\IndexController',
            'Admin\Controller\Login'            => 'Admin\Controller\LoginController',
            'Admin\Controller\Role'             => 'Admin\Controller\RoleController',
            'Admin\Controller\User'             => 'Admin\Controller\UserController',
            'Admin\Controller\Customer'         => 'Admin\Controller\CustomerController',
            'Admin\Controller\VehicleBrands'    => 'Admin\Controller\VehicleBrandsController',
            'Admin\Controller\Zone'             => 'Admin\Controller\ZoneController',
            'Admin\Controller\Product'          => 'Admin\Controller\ProductController',
            'Admin\Controller\QtyDetails'       => 'Admin\Controller\QtyDetailsController',
            'Admin\Controller\Supplier'         => 'Admin\Controller\SupplierController',
            'Admin\Controller\Purchase'         => 'Admin\Controller\PurchaseController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(),
        ),
    ),
);
