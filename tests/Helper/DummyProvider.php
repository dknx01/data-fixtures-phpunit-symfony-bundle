<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Helper;

use Faker\Provider\Base;

class DummyProvider extends Base
{
    public function dummy(): string
    {
        return 'dummy';
    }
}
