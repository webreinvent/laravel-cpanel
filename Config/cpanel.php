<?php

return [
    'protocol'=> 'https',
    'domain' => env('CPANEL_DOMAIN'),
    'port'   => env('CPANEL_PORT', 2083),
    'api_token' => env('CPANEL_API_TOKEN'),
    'username' => env('CPANEL_USERNAME'),
];