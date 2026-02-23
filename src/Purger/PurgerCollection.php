<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Purger;

use Doctrine\Bundle\FixturesBundle\Purger\PurgerFactory;
use Doctrine\Common\DataFixtures\Purger\ORMPurgerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Autoconfigure(public: true)]
class PurgerCollection
{
    /** @var array<string, ORMPurgerInterface> */
    private array $purgerFactories;

    /**
     * @param iterable<RewindableGenerator> $purgers
     */
    public function __construct(
        #[AutowireIterator('doctrine.fixtures.purger_factory', 'alias')] iterable $purgers,
        EntityManagerInterface $em,
    ) {
        /**
         * @var string                            $serviceId
         * @var PurgerFactory<ORMPurgerInterface> $service
         */
        foreach ($purgers as $serviceId => $service) {
            $this->purgerFactories[$serviceId] = $service->createForEntityManager(
                null,
                $em
            );
        }
    }

    public function getPurger(string $name = 'default'): ORMPurgerInterface
    {
        return $this->purgerFactories[$name];
    }
}
