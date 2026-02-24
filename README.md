# Data Fixture For PHPUnit Bundle

This is a Symfony Bundle to use [Symfony DoctrineFixturesBundle](https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html) in PHPUnit test directly.

## Requirements
* [Symfony DoctrineFixturesBundle](https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html)
* [PHPUnit](https://phpunit.de/index.html)
* (optional, but recommended) [Faker](https://fakerphp.org)

## Install
1. Create a config file (see [Minimal Configuration](#minimal-configuration))
2. run `composer require --dev dknx01/data-fixtures-phpunit`

## Usage
If you want to use data fixtures in you tests you can do it in multiple ways.
You can write a method to fill data in the database. or you can use a fixture and reuse it in multiple tests.

### Configuration
Ensure you have the following file:
````yaml
# config/packages/data_fixtures_php_unit.yaml
data_fixtures_php_unit:
  faker:
    locale: 'de_DE' # optional - defaults to en_EN
    providers:
      - 'App\Tests\Faker\Bundeslaender' # optional - defaults to empty array
````
`locale` and `providers` are optional and only needed if you want to change the default behavior.

### Minimal Configuration
The minimal setup would look like:
````yaml
# config/packages/data_fixtures_php_unit.yaml
data_fixtures_php_unit:
  faker:
````

### Fixtures registration
You should have all your Fixture classes registered as a services - at leat inside the test container.

All Fixtures that are in the namespace `App\Tests\Fixtures\` and inside the folder `'%kernel.project_dir%/tests/Fixtures'` are automatically registered.

Example registration:
```yaml
when@test:
  services:
    _defaults:
      autowire: true
      autoconfigure: true
      public: true
    App\Tests\Fixtures\:
      resource: '%kernel.project_dir%/tests/Fixtures'
```

### Writing a fixture
Each fixture must implement the `Doctrine\Common\DataFixtures\FixtureInterface;`.
Example:
``` php
<?php

namespace App\Tests\Fixtures;

use App\Entity\User;
use Dknx01\DataFixturesPhpUnit\Contract\FakerAware;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

readonly class UserFixture implements FixtureInterface, FakerAware
{
    private Generator $faker;

    public function __construct(private string $email = '')
    {
    }

    public function setFaker(Generator $faker): void
    {
        $this->faker = $faker;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail(!empty($this->email) ? $this->email : $this->faker->unique()->safeEmail());
        $user->setPassword('foo');
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);
        $manager->flush();
    }
}

#[DataFixture(new UserFixture('test@fooo.nlkdjlfs'))]
class ApiTest extends ApplicationTestCase {
 // your code
}

#[DataFixture(UserFixture::class)]
class ApiTest extends ApplicationTestCase {
    // your code
}
```
As you can see the fixture can have constructor arguments for individual data in different tests.
#### Data Fixture on method level
Data fixtures can be used on class level (see above) and on method level.
```php
#[DataFixture(UserFixture::class)]
#[DataFixture(new BlaFixture(
    name: 'Test123',
    fileName: 'Test123'
))]
public function testFoo(): void
{
    // cor code
}
```
#### Dependent Fixtures
Fixtures can depend on other fixtures. You can use the way Doctrine data fixtures is suggesting, or you can use an attribute.
```php
#[DependFixture(BarFixture::class)]
class FooFixture implements FixtureInterface, FakerAware
{
    // your code
}
```

### Faker
As you can see it is possible to use PHPFaker inside a fixture class.

If you implement the `FakerAware` interface a Faker instance is automatically injected into the data fixture.

## Limitations
* A fixture class can only be used once for a test, regardless of whether the DataFixture is defined on a class basis or a method basis
  * This is invalid and will only execute on fixture, mostly the latest defined one
```php
    #[DataFixture(new BarFixture('first'))]
    #[DataFixture(BarFixture::class)]
    public function testFoo(): void
    {
    // code
    }
```
