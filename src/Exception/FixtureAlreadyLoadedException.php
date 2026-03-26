<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Exception;

use Dknx01\DataFixturesPhpUnit\Attributes\DataFixture;
use Dknx01\DataFixturesPhpUnit\Attributes\DependFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Exception;

use function sprintf;

class FixtureAlreadyLoadedException extends Exception
{
    public function __construct(DataFixture|DependFixture $dataFixture)
    {
        parent::__construct(sprintf(
            'Fixture "%s" already loaded. Multiple instances are not allowed.',
            $dataFixture->fixtureClass instanceof FixtureInterface ? $dataFixture->fixtureClass::class : $dataFixture->fixtureClass
        )
        );
    }
}
