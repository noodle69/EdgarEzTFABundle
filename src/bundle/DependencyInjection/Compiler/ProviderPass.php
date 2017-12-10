<?php

namespace Edgar\EzTFABundle\DependencyInjection\Compiler;

use Edgar\EzTFA\Handler\AuthHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ProviderPass
 * @package Smile\EzTFABundle\DependencyInjection\Compiler
 */
class ProviderPass implements CompilerPassInterface
{
    /**
     * Process TFA providers
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(AuthHandler::class)) {
            return;
        }

        $definition = $container->findDefinition(AuthHandler::class);
        $taggedServices = $container->findTaggedServiceIds('edgar.tfa.provider');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addProvider', array(
                    new Reference($id),
                    $attributes['alias']
                ));
            }
        }
    }
}
