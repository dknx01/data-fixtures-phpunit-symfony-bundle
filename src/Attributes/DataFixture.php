<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Attributes;

use Attribute;
use Doctrine\Common\DataFixtures\FixtureInterface;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
readonly class DataFixture
{
    /**
     * @param string[] $groups
     */
    public function __construct(
        public string|FixtureInterface $fixtureClass,
        public array $groups = [],
    ) {
    }
}
