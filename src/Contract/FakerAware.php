<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Contract;

use Faker\Generator;

interface FakerAware
{
    public function setFaker(Generator $faker): void;
}
