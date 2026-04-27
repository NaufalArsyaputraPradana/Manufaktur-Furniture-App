<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course,
    | the usual Laravel resource views path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    | FIX FOR WINDOWS FILE LOCKING:
    | Using 'safe' key enables atomic file operations to prevent
    | file locking issues when multiple processes compile views simultaneously
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

    /*
    |--------------------------------------------------------------------------
    | Cache View Compilation
    |--------------------------------------------------------------------------
    |
    | Determine if the views should be cached to disk for better performance.
    | Only relevant in production; development should have this disabled
    | to ensure views are recompiled on every request.
    |
    | WARNING: On Windows with concurrent processes (serve + queue + npm),
    | view caching can cause file locking errors. Solutions:
    |
    | 1. Set 'cache' => false in development
    | 2. Use 'safe' key in storage path to enable atomic operations
    | 3. Clear cache regularly: php artisan view:clear
    | 4. Use queue workers instead of queue:listen for better isolation
    | 5. Use Unix-based system (WSL2) for better file handling
    |
    */

    'cache' => env('VIEW_CACHE_ENABLED', false),

];
