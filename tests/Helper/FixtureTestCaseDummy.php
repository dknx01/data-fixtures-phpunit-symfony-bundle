<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Helper;

use Dknx01\DataFixturesPhpUnit\Attributes\DataFixture;
use Dknx01\DataFixturesPhpUnit\Fixture\DataFixtureTrait;

#[DataFixture(new AnotherFixture())]
#[DataFixture(DependingFixture::class)]
class FixtureTestCaseDummy extends TestKernel
{
    use DataFixtureTrait;

    #[DataFixture(SimpleFixture::class, groups: ['method-group'])]
    public function dummyTestMethod(): void
    {
        $this->loadFixtures();
    }
}
