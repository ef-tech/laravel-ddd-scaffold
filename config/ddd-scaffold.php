<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Domain Namespace
    |--------------------------------------------------------------------------
    |
    | This value determines the root namespace where your DDD structures
    | (Application, Domain, Infrastructure, Support, etc.) will be generated.
    | You can override this with --domain= option in each command.
    |
    */

    'default_domain' => 'Backoffice',

    /*
    |--------------------------------------------------------------------------
    | Custom Stub Path
    |--------------------------------------------------------------------------
    |
    | If you want to override the default stubs, you can set a custom path here.
    | Leave null to use the built-in default stubs provided by this package.
    |
    */

    'stubs_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Testing Framework
    |--------------------------------------------------------------------------
    |
    | This option allows you to choose which test framework you use.
    | Supported values:
    | - 'phpunit' (default) - generates PHPUnit-style test classes
    | - 'pest'              - generates Pest-style test files (function-based)
    |
    | The ddd:make:test command will use this to generate the appropriate test file.
    |
    */

    'testing_framework' => 'phpunit',

];
