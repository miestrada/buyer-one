<?php

namespace App\DependencyInjection\Compiler;

use App\Utilities\Payment\PaymentTypeManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PaymentTypePass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(PaymentTypeManager::class))
            return;

        $definition = $container->findDefinition(PaymentTypeManager::class);

        $taggedServices = $container->findTaggedServiceIds('app.payment_type');

        foreach ($taggedServices as $id => $tags)
            $definition->addMethodCall('addType', [new Reference($id)]);
    }

}