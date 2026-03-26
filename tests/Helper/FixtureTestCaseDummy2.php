<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Helper;

use Dknx01\DataFixturesPhpUnit\Attributes\DataFixture;
use Dknx01\DataFixturesPhpUnit\Fixture\DataFixtureTrait;

class FixtureTestCaseDummy2 extends TestKernel
{
    use DataFixtureTrait;

    #[DataFixture(new ComplexDependingFixture1())]
    public function dummyTestMethod(): void
    {
        $this->loadFixtures();
    }
}
