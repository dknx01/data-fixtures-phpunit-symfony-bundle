<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Unit\Fixture;

use Dknx01\DataFixturesPhpUnit\Fixture\FixtureHandler;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\AnotherFixture;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\ComplexDependingFixture1;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\ComplexDependingFixture2;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\ComplexDependingFixture3;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\DependingFixture;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\FixtureTestCaseDummy;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\FixtureTestCaseDummy2;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\FooFixture;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\Simple2Fixture;
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
        $containerProphecy
            ->get(Simple2Fixture::class)
            ->willReturn(new Simple2Fixture());
        TestKernel::setTestContainer($containerProphecy->reveal());
    }

    public function testLoading(): void
    {
        $testCaseDummy = new FixtureTestCaseDummy('Dummy test Case');

        $testCaseDummy->dummyTestMethod();

        $reflClass = new ReflectionClass($testCaseDummy);
        $reflMethod = $reflClass->getMethod('fixtures');

        $this->assertCount(5, $reflMethod->invoke($testCaseDummy), '5 fixtures should have been passed to the handler');
    }

    public function testLoadingComplexFixture(): void
    {
        $testCaseDummy = new FixtureTestCaseDummy2('Dummy test Case');

        $testCaseDummy->dummyTestMethod();

        $reflClass = new ReflectionClass($testCaseDummy);
        $reflMethod = $reflClass->getMethod('fixtures');

        /** @var non-empty-list<array{
         *     fixture: object,
         *     groups: array<string>}> $fixtures */
        $fixtures = $reflMethod->invoke($testCaseDummy);
        $this->assertCount(3, $fixtures, '3 fixtures should have been passed to the handler');
        $fixtureInstances = array_map(static fn ($el) => $el['fixture'], $fixtures);
        $this->assertNotNull(array_find($fixtureInstances, static fn ($e) => ComplexDependingFixture1::class === $e::class));
        $this->assertNotNull(array_find($fixtureInstances, static fn ($e) => ComplexDependingFixture2::class === $e::class));
        $this->assertNotNull(array_find($fixtureInstances, static fn ($e) => ComplexDependingFixture3::class === $e::class));
    }
}
