<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Unit\Fixture;

use Dknx01\DataFixturesPhpUnit\Exception\FixtureAlreadyLoadedException;
use Dknx01\DataFixturesPhpUnit\Fixture\FixtureHandler;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\AnotherFixture;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\DependingFixture;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\FixtureFailedTestCaseDummy;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\FixtureTestCaseDummy;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\FooFixture;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\SimpleFixture;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\TestKernel;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Container;

class DataFixtureTraitTest extends TestCase
{
    use ProphecyTrait;

    protected function setUp(): void
    {
        $containerProphecy = $this->prophesize(Container::class);

        $containerProphecy
            ->getParameter('data_fixtures_php_unit')
            ->willReturn([
                'faker' => [
                    'locale' => 'en_EN',
                    'providers' => [],
                ],
            ]);

        $handler = new class {
            /**
             * @param mixed[] $data
             */
            public function handle(array $data): void
            {
                // do nothing
            }
        };

        $containerProphecy
            ->has(FixtureHandler::class)
            ->willReturn(true);
        $containerProphecy
            ->get(FixtureHandler::class)
            ->willReturn($handler);

        $containerProphecy
            ->get(SimpleFixture::class)
            ->willReturn(new SimpleFixture());
        $containerProphecy
            ->get(AnotherFixture::class)
            ->willReturn(new AnotherFixture());
        $containerProphecy
            ->get(DependingFixture::class)
            ->willReturn(new DependingFixture());
        $containerProphecy
            ->get(FooFixture::class)
            ->willReturn(new FooFixture());
        TestKernel::setTestContainer($containerProphecy->reveal());
    }

    public function testLoading(): void
    {
        $testCaseDummy = new FixtureTestCaseDummy('Dummy test Case');

        $testCaseDummy->dummyTestMethod();

        $reflClass = new ReflectionClass($testCaseDummy);
        $reflMethod = $reflClass->getMethod('fixtures');

        $this->assertCount(4, $reflMethod->invoke($testCaseDummy), 'One fixture should have been passed to the handler');
    }

    public function testLoadingWithMultipleFixtureInstanceAndException(): void
    {
        $this->expectException(FixtureAlreadyLoadedException::class);
        $this->expectExceptionMessage('Fixture "Dknx01\DataFixturesPhpUnit\Tests\Helper\SimpleFixture" already loaded. Multiple instances are not allowed.');

        $testCaseDummy = new FixtureFailedTestCaseDummy('Dummy test Case');
        $testCaseDummy->dummyTestMethod();
    }
}
