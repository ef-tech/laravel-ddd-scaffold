# Laravel DDD Scaffold

A Laravel package to scaffold DDD (Domain-Driven Design) and Clean Architecture project structure with command-line tools.

## Introduction

Laravel DDD Scaffold is a powerful command-line tool designed to help Laravel developers implement Domain-Driven Design
and Clean Architecture principles in their projects. This package allows you to:

- Separate business logic from the default Laravel `app/` directory
- Organize code into domain-specific modules
- Generate standardized components like UseCases, DTOs, Entitys, Value Objects, and Repositories
- Maintain a consistent project structure across your team

By adopting DDD principles, your Laravel applications become more maintainable, testable, and scalable as they grow in
complexity.

## Installation

You can install the package via composer:

```bash
composer require ef-tech/laravel-ddd-scaffold --dev
```

### Requirements

- Laravel 10.0 or higher
- PHP 8.1 or higher

### Note for Laravel Sail / Docker Users

If you're using Laravel Sail or Docker, make sure to run the commands within your container:

```bash
sail artisan ddd:init YourDomain
```

## Configuration

Publish the configuration file to customize the package behavior:

```bash
php artisan vendor:publish --tag=ddd-scaffold-config
```

This will create a `config/ddd-scaffold.php` file with the following options:

- `default_domain`: The default domain namespace (e.g., `Backoffice`)
- `stubs_path`: Path to custom stub files if you want to override the defaults
- `testing_framework`: Choose between `'phpunit'` (default) or `'pest'` for test generation

## Getting Started

Initialize your DDD structure with a single command:

```bash
php artisan ddd:init Backoffice
```

This will create the following directory structure:

```
Backoffice/
├── Application/
│   ├── DTOs/
│   ├── Enums/
│   ├── Exceptions/
│   ├── Mappers/
│   ├── Presenters/
│   │   └── Entities/
│   ├── Queries/
│   ├── Services/
│   └── UseCases/
├── Domain/
│   ├── Aggregates/
│   ├── Entities/
│   ├── Exceptions/
│   ├── Repositories/
│   ├── Rules/
│   ├── Services/
│   └── ValueObjects/
├── Infrastructure/
│   ├── Enums/
│   ├── Exceptions/
│   ├── Mappers/
│   ├── Repositories/
│   └── Services/
└── Support/
    ├── Constants/
    ├── Enums/
    └── Exceptions/
```

## Available Commands

> **Note:** All commands (except `ddd:init`) support an optional `--domain=YourDomain` argument to specify which domain
> to generate the files in. If not provided, the default domain from your configuration will be used.

### ddd:init

Initializes the base DDD structure for your project.

```bash
php artisan ddd:init {name=MyProject}
```

- `name`: The project name

### ddd:make:usecase

Generates a new UseCase class in the Application layer.

```bash
php artisan ddd:make:usecase {name} [--domain=]
```

- `name`: The name of the use case class
- `--domain`: The domain name

Examples:

```bash
php artisan ddd:make:usecase CreateUser
php artisan ddd:make:usecase User/UpdateProfile
```

### ddd:make:dto

Generates a Data Transfer Object class.

```bash
php artisan ddd:make:dto {name} [--domain=]
```

- `name`: The name of the DTO class
- `--domain`: The domain name

Examples:

```bash
php artisan ddd:make:dto UserData
php artisan ddd:make:dto User/ProfileData
```

### ddd:make:vo

Generates a Value Object class.

```bash
php artisan ddd:make:vo {name} [--domain=]
```

- `name`: The name of the value object class
- `--domain`: The domain name

Examples:

```bash
php artisan ddd:make:vo Email
php artisan ddd:make:vo User/Address
```

### ddd:make:entity

Generates an Entity class.

```bash
php artisan ddd:make:entity {name} [--domain=] [--type=domain]
```

- `name`: The name of the entity class
- `--domain`: The domain name
- `--type`: The layer of the entity (domain, application, presenters)

Examples:

```bash
php artisan ddd:make:entity User
php artisan ddd:make:entity Order/LineItem
php artisan ddd:make:entity UserPresenter --type=presenters
```

### ddd:make:aggregate

Generates a Aggregate class.

```bash
php artisan ddd:make:aggregate {name} [--domain=]
```

- `name`: The name of the aggregate
- `--domain`: The domain name

Examples:

```bash
php artisan ddd:make:aggregate CustomerAggregate
php artisan ddd:make:aggregate Customer/OrderAggregate
```

### ddd:make:repository

Generates a Repository interface and implementation.

```bash
php artisan ddd:make:repository {name} [--domain=]
```

- `name`: The name of the repository
- `--domain`: The domain name

Examples:

```bash
php artisan ddd:make:repository User
php artisan ddd:make:repository Order/OrderItem
```

### ddd:make:enum

Generates an Enum class.

```bash
php artisan ddd:make:enum {name} [--domain=] [--type=domain]
```

- `name`: The name of the enum
- `--domain`: The domain name
- `--type`: The layer of the enum

Examples:

```bash
php artisan ddd:make:enum OrderStatus
php artisan ddd:make:enum User/Role
php artisan ddd:make:enum PaymentStatus --type=application
```

### ddd:make:service

Generates a Service class.

```bash
php artisan ddd:make:service {name} [--domain=] [--type=application]
```

- `name`: The name of the service class
- `--domain`: The domain name
- `--type`: The layer of the service

Examples:

```bash
php artisan ddd:make:service PaymentProcessor
php artisan ddd:make:service Order/ShippingCalculator
php artisan ddd:make:service ProductValidator --type=domain
```

### ddd:make:exception

Generates a custom Exception class.

```bash
php artisan ddd:make:exception {name} [--domain=] [--type=domain]
```

- `name`: The name of the exception class
- `--domain`: The domain name
- `--type`: The layer of the exception

Examples:

```bash
php artisan ddd:make:exception InvalidOrderException
php artisan ddd:make:exception User/AuthenticationFailed
php artisan ddd:make:exception ApiConnectionError --type=infrastructure
```

### ddd:make:query

Generates a Query class for read operations.

```bash
php artisan ddd:make:query {name} [--domain=]
```

- `name`: The name of the query class
- `--domain`: The domain name

Examples:

```bash
php artisan ddd:make:query GetUserList
php artisan ddd:make:query Order/FindByCustomer
```

### ddd:make:presenter

Generates a Presenter class for formatting output.

```bash
php artisan ddd:make:presenter {name} [--domain=]
```

- `name`: The name of the presenter class
- `--domain`: The domain name

Examples:

```bash
php artisan ddd:make:presenter UserPresenter
php artisan ddd:make:presenter Order/OrderSummaryPresenter
```

### ddd:make:mapper

Generates a Mapper class for converting between Domain Entities and either Models or DTOs.

```bash
php artisan ddd:make:mapper {name} [--domain=] [--model=] [--entity=] [--dto=]
```

- `name`: The name of the mapper class
- `--domain`: The domain name
- `--model`: The eloquent model
- `--entity`: The domain entity
- `--dto`: The DTO class

⚠️ You must specify either `--model` or `--dto`, not both.

Examples:

```bash
php artisan ddd:make:mapper CustomerMapper --model=App/Models/Customer --entity=Backoffice/Domain/Entities/Customer
php artisan ddd:make:mapper CustomerMapper --dto=Backoffice/Application/DTOs/CustomerData --entity=Backoffice/Domain/Entities/Customer
```

### ddd:make:rule

Generates a validation Rule class.

```bash
php artisan ddd:make:rule {name} [--domain=]
```

- `name`: The name of the rule class
- `--domain`: The domain name

Examples:

```bash
php artisan ddd:make:rule StrongPassword
php artisan ddd:make:rule Order/ValidDeliveryDate
```

### ddd:make:constant

Generates a Constants class.

```bash
php artisan ddd:make:constant {name} [--domain=]
```

- `name`: The name of the constant class
- `--domain`: The domain name

Examples:

```bash
php artisan ddd:make:constant OrderStatuses
php artisan ddd:make:constant User/Permissions
```

### ddd:make:test

Generates a test class for your components.

```bash
php artisan ddd:make:test {name} [--domain=]
```

- `name`: The name of the test class
- `--domain`: The domain name

The test format (PHPUnit or Pest) is determined by the `testing_framework` setting in your config file.

Examples:

```bash
php artisan ddd:make:test CreateUserUseCaseTest
php artisan ddd:make:test User/EmailValueObjectTest
```

## Examples

Here's an example workflow for creating a user registration feature:

```bash
# Initialize the DDD structure
php artisan ddd:init UserManagement

# Create value objects
php artisan ddd:make:vo Email --domain=UserManagement
php artisan ddd:make:vo Password --domain=UserManagement

# Create DTO for user data
php artisan ddd:make:dto UserRegistrationData --domain=UserManagement

# Create entity
php artisan ddd:make:entity User --domain=UserManagement

# Create aggregate
php artisan ddd:make:aggregate UserAggregate --domain=UserManagement

# Create repository
php artisan ddd:make:repository User --domain=UserManagement

# Create use case
php artisan ddd:make:usecase RegisterUser --domain=UserManagement

# Create test for the use case
php artisan ddd:make:test RegisterUserUseCaseTest --domain=UserManagement
```

In this example, we explicitly specify the `--domain=UserManagement` option for clarity. If you've set `UserManagement`
as your default domain in the config file, you could omit this option.

This will generate a complete set of files following DDD naming conventions and structure:

- `UserManagement/Domain/ValueObjects/Email.php`
- `UserManagement/Domain/ValueObjects/Password.php`
- `UserManagement/Application/DTOs/UserRegistrationData.php`
- `UserManagement/Domain/Entities/User.php`
- `UserManagement/Domain/Aggregates/UserAggregate.php`
- `UserManagement/Domain/Repositories/UserRepositoryInterface.php`
- `UserManagement/Infrastructure/Repositories/UserRepository.php`
- `UserManagement/Application/UseCases/RegisterUserUseCase.php`
- `tests/Unit/UserManagement/Application/UseCases/RegisterUserUseCaseTest.php`

## Testing

The package supports both PHPUnit and Pest testing frameworks. You can configure your preferred framework in the
configuration file:

```php
// config/ddd-scaffold.php
'testing_framework' => 'phpunit', // or 'pest'
```

When using the `ddd:make:test` command, the appropriate test file format will be generated:

### PHPUnit Example

```php
namespace Tests\Unit\Backoffice\Domain\ValueObjects;

use Backoffice\Domain\ValueObjects\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    /** @test */
    public function it_validates_email_format()
    {
        // Test implementation
    }
}
```

### Pest Example

```php
use Backoffice\Domain\ValueObjects\Email;

test('it validates email format', function () {
    // Test implementation
});
```

## License

The MIT License (MIT). Please see the [License File](LICENSE) for more information.
