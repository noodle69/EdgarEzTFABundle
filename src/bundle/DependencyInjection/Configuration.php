<?php

namespace Edgar\EzTFABundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;

class Configuration extends SiteAccessConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('edgar_ez_tfa');

        $systemNode = $this->generateScopeBaseNode($rootNode);
        $systemNode
            ->arrayNode('providers')
                ->prototype('array')
                    ->prototype('scalar')->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
