# Laravel DDD Scaffold

LaravelでDDD（ドメイン駆動設計）とクリーンアーキテクチャのプロジェクト構造をコマンドラインツールで足場固めするためのパッケージです。

## 概要

Laravel DDD Scaffoldは、Laravel開発者がドメイン駆動設計とクリーンアーキテクチャの原則をプロジェクトに導入するのを支援するために設計された、強力なコマンドラインツールです。このパッケージを使用すると、以下のことが可能になります：

- ビジネスロジックをデフォルトのLaravel `app/` ディレクトリから分離
- コードをドメイン固有のモジュールに整理
- ユースケース、DTO、エンティティ、値オブジェクト、リポジトリなどの標準化されたコンポーネントを生成
- チーム全体で一貫したプロジェクト構造を維持

DDDの原則を採用することで、Laravelアプリケーションは複雑さが増しても、保守性、テスト性、拡張性が向上します。

## インストール

composer経由でパッケージをインストールできます：

```bash
composer require ef-tech/laravel-ddd-scaffold --dev
```

### 要件

- Laravel 10.0以上
- PHP 8.1以上

### Laravel Sail / Dockerユーザーへの注意

Laravel SailやDockerを使用している場合は、コンテナ内でコマンドを実行してください：

```bash
sail artisan ddd:init YourDomain
```

## 設定

設定ファイルを公開して、パッケージの動作をカスタマイズします：

```bash
php artisan vendor:publish --tag=ddd-scaffold-config
```

これにより、`config/ddd-scaffold.php`ファイルが作成され、以下のオプションが設定できます：

- `default_domain`: デフォルトのドメイン名前空間（例：`Backoffice`）
- `stubs_path`: デフォルトを上書きしたい場合のカスタムスタブファイルへのパス
- `testing_framework`: テスト生成のために`'phpunit'`（デフォルト）または`'pest'`を選択

## はじめに

1つのコマンドでDDD構造を初期化します：

```bash
php artisan ddd:init Backoffice
```

これにより、以下のディレクトリ構造が作成されます：

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

## 利用可能なコマンド

> **注意:** `ddd:init`を除くすべてのコマンドは、ファイルを生成するドメインを指定するためのオプションの`--domain=YourDomain`引数をサポートしています。指定しない場合は、設定のデフォルトドメインが使用されます。

### ddd:init

プロジェクトの基本的なDDD構造を初期化します。

```bash
php artisan ddd:init {name=MyProject}
```

- `name`: プロジェクト名

### ddd:make:usecase

Applicationレイヤーに新しいUseCaseクラスを生成します。

```bash
php artisan ddd:make:usecase {name} [--domain=]
```

- `name`: ユースケースクラスの名前
- `--domain`: ドメイン名

例：

```bash
php artisan ddd:make:usecase CreateUser
php artisan ddd:make:usecase User/UpdateProfile
```

### ddd:make:dto

データ転送オブジェクト（DTO）クラスを生成します。

```bash
php artisan ddd:make:dto {name} [--domain=]
```

- `name`: DTOクラスの名前
- `--domain`: ドメイン名

例：

```bash
php artisan ddd:make:dto UserData
php artisan ddd:make:dto User/ProfileData
```

### ddd:make:vo

値オブジェクト（Value Object）クラスを生成します。

```bash
php artisan ddd:make:vo {name} [--domain=]
```

- `name`: 値オブジェクトクラスの名前
- `--domain`: ドメイン名

例：

```bash
php artisan ddd:make:vo Email
php artisan ddd:make:vo User/Address
```

### ddd:make:entity

エンティティ（Entity）クラスを生成します。

```bash
php artisan ddd:make:entity {name} [--domain=]
```

- `name`: エンティティクラスの名前
- `--domain`: ドメイン名

例：

```bash
php artisan ddd:make:entity User
php artisan ddd:make:entity Order/LineItem
```

### ddd:make:aggregate

集約（Aggregate）クラスを生成します。

```bash
php artisan ddd:make:aggregate {name} [--domain=]
```

- `name`: 集約の名前
- `--domain`: ドメイン名

例：

```bash
php artisan ddd:make:aggregate CustomerAggregate
php artisan ddd:make:aggregate Customer/OrderAggregate
```

### ddd:make:repository

リポジトリ（Repository）のインターフェースと実装を生成します。

```bash
php artisan ddd:make:repository {name} [--domain=]
```

- `name`: リポジトリの名前
- `--domain`: ドメイン名

例：

```bash
php artisan ddd:make:repository User
php artisan ddd:make:repository Order/OrderItem
```

### ddd:make:enum

Enumクラスを生成します。

```bash
php artisan ddd:make:enum {name} [--domain=] [--type=domain]
```

- `name`: Enumの名前
- `--domain`: ドメイン名
- `--type`: Enumのレイヤー

例：

```bash
php artisan ddd:make:enum OrderStatus
php artisan ddd:make:enum User/Role
php artisan ddd:make:enum PaymentStatus --type=application
```

### ddd:make:service

サービスクラスを生成します。

```bash
php artisan ddd:make:service {name} [--domain=] [--type=application]
```

- `name`: サービスクラスの名前
- `--domain`: ドメイン名
- `--type`: サービスのレイヤー

例：

```bash
php artisan ddd:make:service PaymentProcessor
php artisan ddd:make:service Order/ShippingCalculator
php artisan ddd:make:service ProductValidator --type=domain
```

### ddd:make:exception

カスタム例外クラスを生成します。

```bash
php artisan ddd:make:exception {name} [--domain=] [--type=domain]
```

- `name`: 例外クラスの名前
- `--domain`: ドメイン名
- `--type`: 例外のレイヤー

例：

```bash
php artisan ddd:make:exception InvalidOrderException
php artisan ddd:make:exception User/AuthenticationFailed
php artisan ddd:make:exception ApiConnectionError --type=infrastructure
```

### ddd:make:query

読み取り操作用のクエリクラスを生成します。

```bash
php artisan ddd:make:query {name} [--domain=]
```

- `name`: クエリクラスの名前
- `--domain`: ドメイン名

例：

```bash
php artisan ddd:make:query GetUserList
php artisan ddd:make:query Order/FindByCustomer
```

### ddd:make:presenter

出力フォーマット用のプレゼンタークラスを生成します。

```bash
php artisan ddd:make:presenter {name} [--domain=]
```

- `name`: プレゼンタークラスの名前
- `--domain`: ドメイン名

例：

```bash
php artisan ddd:make:presenter UserPresenter
php artisan ddd:make:presenter Order/OrderSummaryPresenter
```

### ddd:make:mapper

ドメインエンティティとモデルまたはDTOの間で変換するためのマッパークラスを生成します。

```bash
php artisan ddd:make:mapper {name} [--domain=] [--model=] [--entity=] [--dto=]
```

- `name`: マッパークラスの名前
- `--domain`: ドメイン名
- `--model`: Eloquentモデル
- `--entity`: ドメインエンティティ
- `--dto`: DTOクラス

⚠️ `--model`または`--dto`のいずれかを指定する必要がありますが、両方は指定できません。

例：

```bash
php artisan ddd:make:mapper CustomerMapper --model=App/Models/Customer --entity=Backoffice/Domain/Entities/Customer
php artisan ddd:make:mapper CustomerMapper --dto=Backoffice/Application/DTOs/CustomerData --entity=Backoffice/Domain/Entities/Customer
```

### ddd:make:rule

バリデーションルールクラスを生成します。

```bash
php artisan ddd:make:rule {name} [--domain=]
```

- `name`: ルールクラスの名前
- `--domain`: ドメイン名

例：

```bash
php artisan ddd:make:rule StrongPassword
php artisan ddd:make:rule Order/ValidDeliveryDate
```

### ddd:make:constant

定数クラスを生成します。

```bash
php artisan ddd:make:constant {name} [--domain=]
```

- `name`: 定数クラスの名前
- `--domain`: ドメイン名

例：

```bash
php artisan ddd:make:constant OrderStatuses
php artisan ddd:make:constant User/Permissions
```

### ddd:make:test

コンポーネントのテストクラスを生成します。

```bash
php artisan ddd:make:test {name} [--domain=]
```

- `name`: テストクラスの名前
- `--domain`: ドメイン名

テスト形式（PHPUnitまたはPest）は、設定ファイルの`testing_framework`設定によって決まります。

例：

```bash
php artisan ddd:make:test CreateUserUseCaseTest
php artisan ddd:make:test User/EmailValueObjectTest
```

## 事例

ユーザー登録機能を作成するためのワークフローの例です：

```bash
# DDD構造を初期化
php artisan ddd:init UserManagement

# 値オブジェクトを作成
php artisan ddd:make:vo Email --domain=UserManagement
php artisan ddd:make:vo Password --domain=UserManagement

# ユーザーデータ用のDTOを作成
php artisan ddd:make:dto UserRegistrationData --domain=UserManagement

# エンティティを作成
php artisan ddd:make:entity User --domain=UserManagement

# 集約を作成
php artisan ddd:make:aggregate UserAggregate --domain=UserManagement

# リポジトリを作成
php artisan ddd:make:repository User --domain=UserManagement

# ユースケースを作成
php artisan ddd:make:usecase RegisterUser --domain=UserManagement

# ユースケースのテストを作成
php artisan ddd:make:test RegisterUserUseCaseTest --domain=UserManagement
```

この例では、明確にするために`--domain=UserManagement`オプションを明示的に指定しています。設定ファイルで`UserManagement`をデフォルトドメインとして設定している場合は、このオプションを省略できます。

これにより、DDDの命名規則と構造に従ったファイルの完全なセットが生成されます：

- `UserManagement/Domain/ValueObjects/Email.php`
- `UserManagement/Domain/ValueObjects/Password.php`
- `UserManagement/Application/DTOs/UserRegistrationData.php`
- `UserManagement/Domain/Entities/User.php`
- `UserManagement/Domain/Aggregates/UserAggregate.php`
- `UserManagement/Domain/Repositories/UserRepositoryInterface.php`
- `UserManagement/Infrastructure/Repositories/UserRepository.php`
- `UserManagement/Application/UseCases/RegisterUserUseCase.php`
- `tests/Unit/UserManagement/Application/UseCases/RegisterUserUseCaseTest.php`

## テスト

このパッケージは、PHPUnitとPestの両方のテストフレームワークをサポートしています。設定ファイルで好みのフレームワークを設定できます：

```php
// config/ddd-scaffold.php
'testing_framework' => 'phpunit', // または 'pest'
```

`ddd:make:test`コマンドを使用すると、適切なテストファイル形式が生成されます：

### PHPUnitの例

```php
namespace Tests\Unit\Backoffice\Domain\ValueObjects;

use Backoffice\Domain\ValueObjects\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    /** @test */
    public function it_validates_email_format()
    {
        // テスト実装
    }
}
```

### Pestの例

```php
use Backoffice\Domain\ValueObjects\Email;

test('it validates email format', function () {
    // テリ
});
```

## ライセンス

MITライセンス（MIT）。詳細については、[ライセンスファイル](LICENSE)を参照してください。
