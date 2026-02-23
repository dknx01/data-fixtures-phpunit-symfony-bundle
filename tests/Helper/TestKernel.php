<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Helper;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

class TestKernel extends KernelTestCase
{
    protected static Container $testContainer;

    public static function setTestContainer(Container $container): void
    {
        self::$testContainer = $container;
    }

    public static function getContainer(): Container
    {
        // KernelTestCase::getContainer() is final in Symfony 6, but for the
        // purpose of this isolated test we can safely override it here.
        return self::$testContainer;
    }
}
