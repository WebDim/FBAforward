<?php return [
    'qbo_token' => env('QUICKBOOK_TOKEN'),
    'qbo_consumer_key' => env('QBO_OAUTH_CONSUMER_KEY'),
    'qbo_consumer_secret' => env('QBO_CONSUMER_SECRET'),
    'qbo_sandbox' => env('QBO_SANDBOX'),
    'qbo_encryption_key' => env('QBO_ENCRYPTION_KEY'),
    'qbo_username' => env('QBO_USERNAME'),
    'qbo_tenant' => env('QBO_TENANT'),
    'qbo_auth_url' => 'http://app.yoursite.com/qbo/oauth',
    'qbo_success_url' => 'http://app.yoursite.com/qbo/success',
    'qbo_mysql_connection' => 'mysqli://'. env('DB_USERNAME') .':'. env('DB_PASSWORD') .'@'. env('DB_HOST') .'/'. env('DB_DATABASE')
];