<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Frontend Stack Toggle
    |--------------------------------------------------------------------------
    |
    | Controls which frontend stack serves the primary (root) URLs.
    |
    | 'react' → React/Inertia routes serve at root (/login, /dashboard, etc.)
    |            Legacy Blade routes move to /legacy/ prefix (Super Admin only)
    |
    | 'blade' → Legacy Blade routes serve at root (original behavior)
    |            React/Inertia routes remain at /v2/ prefix
    |
    | To switch in an emergency:
    |   1. Change FRONTEND_STACK in .env
    |   2. Run: php artisan config:clear
    |
    */

    'stack' => env('FRONTEND_STACK', 'react'),

];
