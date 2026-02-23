<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Helper;

use Dknx01\DataFixturesPhpUnit\Faker\FakerTrait;
use Faker\Generator;

class FakerAwareDummy
{
    use FakerTrait;

    // expose the protected methods for the test
    public function publicLoadFaker(): void
    {
        $this->loadFaker();
    }

    public static function publicCreateFaker(): Generator
    {
        return self::createFaker();
    }

    public function publicGetFaker(): Generator
    {
        return $this->getFaker();
    }

    public static function getFakerLocale(): string
    {
        return self::$fakerLocale;
    }

    /**
     * @return string[]
     */
    public static function getFakerProviders(): array
    {
        return self::$fakerProviders;
    }

    public static function setFakerLocale(string $fakerLocale): void
    {
        self::$fakerLocale = $fakerLocale;
    }

    /**
     * @param string[] $fakerProviders
     */
    public static function setFakerProviders(array $fakerProviders): void
    {
        self::$fakerProviders = $fakerProviders;
    }
}
