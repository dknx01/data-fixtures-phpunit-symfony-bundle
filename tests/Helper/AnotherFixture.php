<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Helper;

use Dknx01\DataFixturesPhpUnit\Contract\FakerAware;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class AnotherFixture implements FixtureInterface, FakerAware
{
    private Generator $faker;

    public function setFaker(Generator $faker): void
    {
        $this->faker = $faker;
    }

    public function load(ObjectManager $manager): void
    {
    }
}
