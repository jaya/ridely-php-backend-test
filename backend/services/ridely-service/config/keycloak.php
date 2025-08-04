<?php
return [
    'health_check_url' => env('KEYCLOAK_HEALTH_CHECK_URL', 'http://localhost:8080/realms/master'),
    'issuer' => env('KEYCLOAK_ISSUER', 'http://localhost:8080/realms/spa-customers'),
    'realm_url' => env('KEYCLOAK_REALM_URL', 'http://localhost:8080/realms/spa-customers'),
    'client_id' => env('KEYCLOAK_CLIENT_ID', 'ridely-service'),
    'client_secret' => env('KEYCLOAK_CLIENT_SECRET', 'secret_not_defined'),
];