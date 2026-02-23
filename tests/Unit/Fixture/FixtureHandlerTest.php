<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Unit\Fixture;

use Dknx01\DataFixturesPhpUnit\Fixture\FixtureHandler;
use Dknx01\DataFixturesPhpUnit\Purger\PurgerCollection;
use Dknx01\DataFixturesPhpUnit\Tests\Helper\SimpleFixture;
use Doctrine\Bundle\FixturesBundle\Purger\PurgerFactory;
use Doctrine\Common\DataFixtures\Purger\ORMPurgerInterface;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class FixtureHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testHandle(): void
    {
        $emProhecy = $this->prophesize(EntityManagerInterface::class);
        $emProhecy->getEventManager()->shouldBeCalled()->willReturn($this->prophesize(EventManager::class)->reveal());
        $emProhecy->wrapInTransaction(Argument::any())->shouldBeCalled()->willReturn(null);

        $em = $emProhecy->reveal();
        $purger = $this->prophesize(ORMPurgerInterface::class)->reveal();

        $purgerFactory = $this->prophesize(PurgerFactory::class);
        $purgerFactory->createForEntityManager(null, $em)->shouldBeCalled()->willReturn($purger);

        $purgerCollection = new PurgerCollection(['default' => $purgerFactory->reveal()], $em);

        $handler = new FixtureHandler($purgerCollection, $em);
        $handler->handle([['fixture' => new SimpleFixture(), 'groups' => []]]);
    }
}
