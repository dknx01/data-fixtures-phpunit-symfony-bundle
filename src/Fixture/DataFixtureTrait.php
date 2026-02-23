<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Fixture;

use Dknx01\DataFixturesPhpUnit\Attributes\DataFixture;
use Dknx01\DataFixturesPhpUnit\Attributes\DependFixture;
use Dknx01\DataFixturesPhpUnit\Contract\FakerAware;
use Dknx01\DataFixturesPhpUnit\Exception\FixtureAlreadyLoadedException;
use Dknx01\DataFixturesPhpUnit\Faker\FakerTrait;
use Doctrine\Common\DataFixtures\FixtureInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

use const DEBUG_BACKTRACE_IGNORE_ARGS;

trait DataFixtureTrait
{
    use FakerTrait;

    /** @var list<array{fixture: FixtureInterface, groups:list<string>}> */
    private array $fixtures = [];

    /** @return  list<array{fixture: FixtureInterface, groups:list<string>}> */
    protected function fixtures(): array
    {
        return $this->fixtures;
    }

    private function prepareFaker(): void
    {
        $configuration = self::getContainer()->getParameter('data_fixtures_php_unit');
        self::$fakerLocale = $configuration['faker']['locale'];
        self::$fakerProviders = $configuration['faker']['providers'];
    }

    /**
     * @throws ReflectionException
     * @throws FixtureAlreadyLoadedException
     */
    protected function loadFixtures(int $stackPosition = 1): void
    {
        $this->prepareFaker();

        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $reflection = new ReflectionClass($stack[$stackPosition]['class']);
        foreach ($reflection->getAttributes(DataFixture::class) as $attribute) {
            /** @var DataFixture $attrInstance */
            $attrInstance = $attribute->newInstance();
            $this->addFixture($attrInstance);
        }

        $reflection = new ReflectionMethod($stack[$stackPosition]['class'], $stack[$stackPosition]['function']);
        foreach ($reflection->getAttributes(DataFixture::class) as $attribute) {
            /** @var DataFixture $attrInstance */
            $attrInstance = $attribute->newInstance();
            $this->addFixture($attrInstance);
        }
        $this->processDependentFixtures();
        self::getContainer()->get(FixtureHandler::class)?->handle($this->fixtures);
    }

    /**
     * @throws FixtureAlreadyLoadedException
     */
    private function addFixture(DataFixture $dataFixture, bool $toTop = false): void
    {
        $fixtureAlreadyLoaded = $this->fixtureAlreadyLoaded($dataFixture);
        if (null !== $fixtureAlreadyLoaded) {
            throw new FixtureAlreadyLoadedException($dataFixture);
        }
        $fixture = $this->getFixture($dataFixture);
        $fixtureStruct = [
            'fixture' => $fixture,
            'groups' => $dataFixture->groups,
        ];
        if (!$toTop) {
            $this->fixtures[] = $fixtureStruct;
        } else {
            array_unshift($this->fixtures, $fixtureStruct);
        }
    }

    private function fixtureAlreadyLoaded(DependFixture|DataFixture $attrInstance): int|string|null
    {
        return array_find_key($this->fixtures, static fn (array $f) => $f['fixture']::class === ($attrInstance->fixtureClass instanceof FixtureInterface ? $attrInstance->fixtureClass::class : $attrInstance->fixtureClass)
        );
    }

    private function getFixture(DataFixture $attrInstance): FixtureInterface
    {
        $fixture = $attrInstance->fixtureClass instanceof FixtureInterface
            ? $attrInstance->fixtureClass
            : self::getContainer()->get($attrInstance->fixtureClass);
        if ($fixture instanceof FakerAware) {
            $fixture->setFaker($this->getFaker());
        }

        return $fixture;
    }

    /**
     * @throws ReflectionException
     */
    public function processDependentFixtures(): void
    {
        foreach ($this->fixtures as $fixture) {
            $reflection = new ReflectionClass($fixture['fixture']);
            foreach ($reflection->getAttributes(DependFixture::class) as $attribute) {
                /** @var DependFixture $attrInstance */
                $attrInstance = $attribute->newInstance();
                $dependFixture = new DataFixture($attrInstance->fixtureClass, $attrInstance->groups);
                try {
                    $this->addFixture($dependFixture, true);
                } catch (FixtureAlreadyLoadedException $e) {
                    // Do nothing as the dependent fixture was only already loaded, which may occur.
                }
            }
        }
    }
}
