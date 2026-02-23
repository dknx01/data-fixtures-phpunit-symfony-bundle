<?php

/*
 * This file is part of the dknx01/data-fixtures-phpunit-symfony-bundle package.
 * (c) dknx01/data-fixtures-phpunit
 */

namespace Dknx01\DataFixturesPhpUnit;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class DataFixturesPhpUnitBundle extends AbstractBundle
{
    protected string $extensionAlias = 'data_fixtures_php_unit';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('faker')
                    ->isRequired()
                    ->info('Configuration for the faker setup of the data fixtures')
                    ->children()
                        ->scalarNode('locale')
                            ->defaultValue('en_EN')
                            ->info('Faker locale for generating localized data')
                            ->cannotBeEmpty()
                        ->end() // end  locale
                        ->arrayNode('providers')
                            ->prototype('scalar')->end()
                            ->info('List of Faker provider class names for providing  custom Faker data')
                            ->defaultValue([])
                        ->end() // end providers
                    ->end() // end faker children
                ->end() // end faker
            ->end() // end root children
        ;
    }

    /**
     * @param array<array-key, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
        $container->parameters()->set('data_fixtures_php_unit', $config);
    }
}
