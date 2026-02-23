<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Helper;

use Dknx01\DataFixturesPhpUnit\Attributes\DependFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

#[DependFixture(AnotherFixture::class)]
#[DependFixture(FooFixture::class)]
class DependingFixture implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
    }
}
