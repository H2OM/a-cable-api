<?php

use app\middleware\AdminMiddleware;

/** Карта защищенных маршрутов
 *
 *                      [
 *                          'имя-контроллера' => [
 *                              'класс' => 'middleware-обработчик',
 *                              'базовое право доступа' => 'Если не нашлось прав для запрашиваемого экшена',
 *                              'экшен' => 'Право доступа | '' | false (без авторизации по JWT)'
 *                      ]
 * @var false[][]|string[][] $PROTECTED_ROUTES
 */
return [
    'admin-auth' => [
        'class' => AdminMiddleware::class,
        'base_permission' => '',
        'check' => '',
        'login' => false
    ],
    'admin-user' => [
        'class' => AdminMiddleware::class,
        'base_permission' => 'user',
        'delete' => 'user.delete',
    ],
    'admin-products' => [
        'class' => AdminMiddleware::class,
        'base_permission' => 'products',
        'pair-variations' => 'products.edit',
        'pair-related' => 'products.edit',
        'update' => 'products.edit',
        'exclude-hit' => 'products.edit',
        'make-hit' => 'products.edit',
        'add' => 'products.add',
        'delete' => 'products.delete',
    ],
    'admin-parser' => [
        'class' => AdminMiddleware::class,
        'base_permission' => 'parser',
    ],
    'admin-orders' => [
        'class' => AdminMiddleware::class,
        'base_permission' => 'orders',
        'update' => 'orders.edit',
        'update-status' => 'orders.edit',
        'delete' => 'orders.delete',
    ],
    'admin-filters' => [
        'class' => AdminMiddleware::class,
        'base_permission' => 'filters',
        'delete' => 'filters.delete'
    ],
    'admin-categories' => [
        'class' => AdminMiddleware::class,
        'base_permission' => 'categories',
        'delete' => 'categories.delete'
    ],
    'admin-brands' => [
        'class' => AdminMiddleware::class,
        'base_permission' => 'brands',
        'delete' => 'brands.delete',
        'safe-delete' => 'brands.delete'
    ],
    'admin-banners' => [
        'class' => AdminMiddleware::class,
        'base_permission' => 'banners'
    ],
    'admin-callbacks' => [
        'class' => AdminMiddleware::class,
        'base_permission' => 'callbacks',
        'delete' => 'callbacks.delete'
    ],
];