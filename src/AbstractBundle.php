<?php

/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spipu\CoreBundle;

use Spipu\CoreBundle\Service\RoleDefinitionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle as SymfonyAbstractBundle;

abstract class AbstractBundle extends SymfonyAbstractBundle implements RolesHierarchyBundleInterface
{
    /**
     * @param array $config
     * @param ContainerConfigurator $container
     * @param ContainerBuilder $builder
     * @return void
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
    }

    public function getRolesHierarchy(): ?RoleDefinitionInterface
    {
        return null;
    }
}
