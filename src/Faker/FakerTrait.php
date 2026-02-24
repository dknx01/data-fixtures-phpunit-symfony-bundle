<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Faker;

use Faker\Factory;
use Faker\Generator;

trait FakerTrait
{
    /** @var string[] */
    protected static array $fakerProviders = [];
    protected static string $fakerLocale = 'en_EN';
    protected Generator $faker;

    protected function loadFaker(): void
    {
        $this->faker = Factory::create(self::$fakerLocale);
        foreach (self::$fakerProviders as $fakerProvider) {
            $this->faker->addProvider(new $fakerProvider($this->faker));
        }
    }

    protected static function setFakerLocale(string $fakerLocale): void
    {
        self::$fakerLocale = $fakerLocale;
    }

    /**
     * @param class-string[] $fakerProviders
     */
    protected static function setFakerProviders(array $fakerProviders): void
    {
        self::$fakerProviders = $fakerProviders;
    }

    protected static function createFaker(): Generator
    {
        $faker = Factory::create(self::$fakerLocale);
        foreach (self::$fakerProviders as $fakerProvider) {
            $faker->addProvider(new $fakerProvider($faker));
        }

        return $faker;
    }

    protected function getFaker(): Generator
    {
        if (!isset($this->faker)) {
            $this->loadFaker();
        }

        return $this->faker;
    }
}
