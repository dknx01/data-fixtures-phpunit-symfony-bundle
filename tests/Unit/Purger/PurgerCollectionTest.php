<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Unit\Purger;

use ArrayIterator;
use Dknx01\DataFixturesPhpUnit\Purger\PurgerCollection;
use Doctrine\Bundle\FixturesBundle\Purger\PurgerFactory;
use Doctrine\Common\DataFixtures\Purger\ORMPurgerInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PurgerCollectionTest extends TestCase
{
    use ProphecyTrait;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->entityManager = $entityManager->reveal();
    }

    public function testInstance(): void
    {
        $purgerProphecy = $this->prophesize(ORMPurgerInterface::class);
        $purger = $purgerProphecy->reveal();

        $purgerFactory = $this->prophesize(PurgerFactory::class);
        $purgerFactory
            ->createForEntityManager(null, $this->entityManager)
            ->willReturn($purger)
            ->shouldBeCalled();
        $factory = $purgerFactory->reveal();

        $purgers = new ArrayIterator(['default' => $factory]);

        $collection = new PurgerCollection($purgers, $this->entityManager);

        $this->assertSame($purger, $collection->getPurger());
    }
}
