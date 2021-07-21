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

namespace Spipu\CoreBundle\DependencyInjection;

use Spipu\CoreBundle\Entity\Role\Item;
use Spipu\CoreBundle\Service\RoleDefinition;
use Spipu\CoreBundle\Service\RoleDefinitionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class SpipuCoreExtension extends Extension implements
    PrependExtensionInterface,
    RolesHierarchyExtensionExtensionInterface
{
    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @return void
     * @throws \Exception
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     * @param ContainerBuilder $container
     * @return void
     * @SuppressWarnings(PMD.StaticAccess)
     */
    public function prepend(ContainerBuilder $container)
    {
        foreach ($container->getExtensions() as $extension) {
            if (!($extension instanceof RolesHierarchyExtensionExtensionInterface)) {
                continue;
            }
            $extension->getRolesHierarchy()->buildDefinition();
        }

        $rolesHierarchy = [];
        foreach (Item::getAll() as $role) {
            if (count($role->getChildren()) === 0) {
                continue;
            }
            $rolesHierarchy[$role->getCode()] = array_keys($role->getChildren());
        }

        $container->prependExtensionConfig('security', ['role_hierarchy' => $rolesHierarchy]);
    }

    /**
     * @return RoleDefinitionInterface
     */
    public function getRolesHierarchy(): RoleDefinitionInterface
    {
        return new RoleDefinition();
    }
}
