<?php

declare(strict_types=1);

namespace Abenmada\ExportPlugin\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class ExportExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs); // @phpstan-ignore-line

        $container->setParameter('abenmada_export_plugin.resources_definitions', $config['resource']);
    }

    public function prepend(ContainerBuilder $container): void
    {
    }

    public function getAlias(): string
    {
        return 'abenmada_export_plugin';
    }
}
