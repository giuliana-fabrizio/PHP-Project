<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/' => [[['_route' => 'app_index', '_controller' => 'App\\Controller\\AppController::index'], null, null, null, false, false, null]],
        '/event' => [[['_route' => 'app_createevent', '_controller' => 'App\\Controller\\AppController::createEvent'], null, null, null, false, false, null]],
        '/events' => [[['_route' => 'event_list', '_controller' => 'App\\Controller\\EventController::getEvents'], null, null, null, false, false, null]],
        '/event_filter' => [[['_route' => 'event_filter', '_controller' => 'App\\Controller\\EventController::filterEvents'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [
            [['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
