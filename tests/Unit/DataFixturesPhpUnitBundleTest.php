<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit\Tests\Unit;

use Dknx01\DataFixturesPhpUnit\DataFixturesPhpUnitBundle;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\Definition\Loader\DefinitionFileLoader;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DataFixturesPhpUnitBundleTest extends TestCase
{
    use ProphecyTrait;

    private DataFixturesPhpUnitBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new DataFixturesPhpUnitBundle();
    }

    public function testConfigureBuildsDefinitionTree(): void
    {
        $treeBuilder = new TreeBuilder('Foo');
        $locator = $this->prophesize(FileLocatorInterface::class);
        $container = $this->prophesize(ContainerBuilder::class);
        $definitionLoader = new DefinitionFileLoader($treeBuilder, $locator->reveal(), $container->reveal());
        $definition = new DefinitionConfigurator($treeBuilder, $definitionLoader, '', '');

        $this->bundle->configure($definition);
        $buildedTree = $treeBuilder->buildTree()->getChildren();
        $this->assertArrayHasKey('faker', $buildedTree);
        $this->assertArrayHasKey('locale', $buildedTree['faker']->getChildren());
        $this->assertArrayHasKey('providers', $buildedTree['faker']->getChildren());
    }
}
