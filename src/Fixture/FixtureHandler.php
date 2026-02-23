<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Fixture;

use Dknx01\DataFixturesPhpUnit\Purger\PurgerCollection;
use Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Autoconfigure(public: true)]
readonly class FixtureHandler
{
    public function __construct(
        private PurgerCollection $purgers,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @param list<array{fixture: FixtureInterface, groups:list<string>}> $fixtures
     */
    public function handle(array $fixtures, bool $append = false): void
    {
        $fixturesProvider = new SymfonyFixturesLoader();
        array_walk($fixtures, static function (&$value) {
            if (empty($value['groups'])) {
                $value['groups'] = ['default'];
            }
        });

        $fixturesProvider->addFixtures($fixtures);
        $executor = new ORMExecutor($this->em, $this->purgers->getPurger());
        $executor->execute($fixturesProvider->getFixtures(), $append);
    }
}
