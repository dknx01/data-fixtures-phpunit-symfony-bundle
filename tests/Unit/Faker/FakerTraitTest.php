<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Unit\Faker;

use Dknx01\DataFixturesPhpUnit\Tests\Helper\DummyProvider;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\FakerAwareDummy;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FakerTraitTest extends TestCase
{
    public function testLoadFakerWithDefaultValues(): void
    {
        $dummy = new FakerAwareDummy();
        $this->assertEquals('en_EN', $dummy::getFakerLocale());

        $this->assertEquals([], $dummy::getFakerProviders());
    }

    public function testLoadFakerWithProviders(): void
    {
        $dummy = new FakerAwareDummy();
        $dummy::setFakerProviders([DummyProvider::class]);
        $this->assertEquals('dummy', $dummy->publicGetFaker()->dummy());
    }

    public function testLoadFakerStaticWithLocale(): void
    {
        FakerAwareDummy::setFakerLocale('de_DE');
        $instance = FakerAwareDummy::publicCreateFaker();
        $reflection = new ReflectionClass($instance);
        $property = $reflection->getProperty('providers');

        $textProvider = array_filter($property->getValue($instance), static fn ($item) => 'Faker\Provider\de_DE\Text' === $item::class);
        $this->assertCount(1, $textProvider);
    }
}
