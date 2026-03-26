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
        $this->processClass($stack[$stackPosition]['class']);

        $reflection = new ReflectionMethod($stack[$stackPosition]['class'], $stack[$stackPosition]['function']);
        foreach ($reflection->getAttributes(DataFixture::class) as $attribute) {
            /** @var DataFixture $attrInstance */
            $attrInstance = $attribute->newInstance();
            $reflFixture = new ReflectionClass($attrInstance->fixtureClass);
            foreach ($reflFixture->getAttributes(DependFixture::class) as $fixture) {
                /** @var DependFixture $newInstance */
                $newInstance = $fixture->newInstance();
                $this->processClass($newInstance->fixtureClass);
                $this->addFixture($newInstance);
            }
            try {
                $this->addFixture($attrInstance);
            } catch (FixtureAlreadyLoadedException $e) {
                // do nothing as the fixture was already loaded
            }
        }
        self::getContainer()->get(FixtureHandler::class)?->handle($this->fixtures);
    }

    private function processClass(string|object $class): void
    {
        $reflection = new ReflectionClass($class);
        if ($reflection->implementsInterface(FixtureInterface::class)) {
            foreach ($reflection->getAttributes(DependFixture::class) as $f) {
                /** @var DependFixture $newInstance */
                $newInstance = $f->newInstance();
                $this->processClass($newInstance->fixtureClass);
                $this->addFixture($newInstance);
            }
        }
        foreach ($reflection->getAttributes(DataFixture::class) as $attribute) {
            /** @var DataFixture $attrInstance */
            $attrInstance = $attribute->newInstance();
            $reflFixture = new ReflectionClass($attrInstance->fixtureClass);
            foreach ($reflFixture->getAttributes(DependFixture::class) as $fixture) {
                /** @var DataFixture $fixtureClass */
                $fixtureClass = $fixture->newInstance();
                $this->processClass($fixtureClass->fixtureClass);
                try {
                    $this->addFixture($fixtureClass);
                } catch (FixtureAlreadyLoadedException $e) {
                    // do nothing as fixture already loaded
                }
            }
            try {
                $this->addFixture($attrInstance);
            } catch (FixtureAlreadyLoadedException $e) {
                // do nothing as fixture is already loaded
            }
        }
    }

    /**
     * @throws FixtureAlreadyLoadedException
     */
    private function addFixture(DataFixture|DependFixture $dataFixture, bool $toTop = false): void
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

    private function getFixture(DataFixture|DependFixture $attrInstance): FixtureInterface
    {
        $fixture = $attrInstance->fixtureClass instanceof FixtureInterface
            ? $attrInstance->fixtureClass
            : self::getContainer()->get($attrInstance->fixtureClass);
        if ($fixture instanceof FakerAware) {
            $fixture->setFaker($this->getFaker());
        }

        return $fixture;
    }
}
