<?php

declare(strict_types=1);

namespace Abenmada\ExportPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('abenmada_export_plugin');
        $rootNode = $treeBuilder->getRootNode();

        $this->addModelsSection($rootNode);

        return $treeBuilder;
    }

    private function addModelsSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resource')
                    ->useAttributeAsKey('alias')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('model')->cannotBeEmpty()->end()

                            ->arrayNode('download')
                                ->children()
                                    ->enumNode('type')->values(['xlsx', 'xls', 'csv', 'pdf', 'ods', 'html'])->defaultValue('xlsx')->end()
                                    ->booleanNode('enabled')->defaultTrue()->end()
                                    ->booleanNode('auto_size')->defaultTrue()->end()
                                    ->booleanNode('prefix_timestamp')->defaultFalse()->end()
                                    ->booleanNode('suffix_timestamp')->defaultFalse()->end()
                                    ->scalarNode('file_name')->defaultValue('export')->end()
                                    ->arrayNode('metadata')
                                        ->children()
                                            ->scalarNode('creator')->end()
                                            ->scalarNode('lastModifiedBy')->end()
                                            ->scalarNode('title')->end()
                                            ->scalarNode('subject')->end()
                                            ->scalarNode('description')->end()
                                            ->scalarNode('keywords')->end()
                                            ->scalarNode('category')->end()
                                            ->scalarNode('manager')->end()
                                            ->scalarNode('company')->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('style')
                                        ->children()
                                            ->scalarNode('size')->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('security')
                                        ->children()
                                            ->booleanNode('enabled')->defaultFalse()->end()
                                            ->scalarNode('password')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()

                            ->arrayNode('save')
                                ->children()
                                    ->enumNode('type')->values(['xlsx', 'xls', 'csv', 'pdf', 'ods', 'html'])->defaultValue('xlsx')->end()
                                    ->booleanNode('enabled')->defaultFalse()->end()
                                    ->booleanNode('auto_size')->defaultTrue()->end()
                                    ->booleanNode('prefix_timestamp')->defaultFalse()->end()
                                    ->booleanNode('suffix_timestamp')->defaultFalse()->end()
                                    ->scalarNode('file_name')->defaultValue('export')->end()
                                    ->scalarNode('path')->isRequired()->end()
                                    ->arrayNode('metadata')
                                        ->children()
                                            ->scalarNode('creator')->end()
                                            ->scalarNode('lastModifiedBy')->end()
                                            ->scalarNode('title')->end()
                                            ->scalarNode('subject')->end()
                                            ->scalarNode('description')->end()
                                            ->scalarNode('keywords')->end()
                                            ->scalarNode('category')->end()
                                            ->scalarNode('manager')->end()
                                            ->scalarNode('company')->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('style')
                                        ->children()
                                            ->scalarNode('size')->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('security')
                                        ->children()
                                            ->booleanNode('enabled')->defaultFalse()->end()
                                            ->scalarNode('password')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()

                            ->arrayNode('repository')
                                ->children()
                                    ->scalarNode('method')->isRequired()->end()
                                    ->arrayNode('arguments')
                                        ->performNoDeepMerging()
                                        ->variablePrototype()->end()
                                    ->end()
                                ->end()
                            ->end()

                            ->arrayNode('properties')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('label')->cannotBeEmpty()->end()
                                        ->scalarNode('path')->cannotBeEmpty()->end()
                                        ->booleanNode('enabled')->defaultTrue()->end()
                                        ->integerNode('position')->defaultValue(1)->min(1)->end()
                                        ->arrayNode('options')
                                            ->children()
                                                ->scalarNode('format')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
