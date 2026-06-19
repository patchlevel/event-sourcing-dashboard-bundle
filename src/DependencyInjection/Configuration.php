<?php

declare(strict_types=1);

namespace Patchlevel\EventSourcingDashboardBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /** @return TreeBuilder<'array'> */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('patchlevel_event_sourcing_dashboard');

        // @codingStandardsIgnoreStart
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->children()
            ->scalarNode('enabled')->defaultFalse()->end()
        ->end();
        // @codingStandardsIgnoreEnd

        return $treeBuilder;
    }
}
