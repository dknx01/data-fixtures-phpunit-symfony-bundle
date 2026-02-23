<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Helper;

use Dknx01\DataFixturesPhpUnit\Attributes\DataFixture;
use Dknx01\DataFixturesPhpUnit\Fixture\DataFixtureTrait;

class FixtureFailedTestCaseDummy extends TestKernel
{
    use DataFixtureTrait;

    #[DataFixture(SimpleFixture::class, groups: ['method-group'])]
    #[DataFixture(SimpleFixture::class, groups: ['foo-group'])]
    public function dummyTestMethod(): void
    {
        $this->loadFixtures();
    }
}
