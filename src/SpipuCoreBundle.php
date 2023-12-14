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

use ReflectionObject;
use Spipu\CoreBundle\Entity\Role\Item;
use Spipu\CoreBundle\Service\RoleDefinition;
use Spipu\CoreBundle\Service\RoleDefinitionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\BundleExtension;

class SpipuCoreBundle extends AbstractBundle
{
    /**
     * @param ContainerConfigurator $container
     * @param ContainerBuilder $builder
     * @return void
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     * @SuppressWarnings(PMD.StaticAccess)
     */
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        foreach ($builder->getExtensions() as $extension) {
            if ($extension instanceof RolesHierarchyBundleInterface) {
                $this->loadRolesHierarchy($extension);
                continue;
            }

            if ($extension instanceof BundleExtension) {
                $reflection = new ReflectionObject($extension);
                $property = $reflection->getProperty('subject');
                $bundle = $property->getValue($extension);
                if ($bundle instanceof RolesHierarchyBundleInterface) {
                    $this->loadRolesHierarchy($bundle);
                }
            }
        }

        $rolesHierarchy = [];
        foreach (Item::getAll() as $role) {
            if (count($role->getChildren()) === 0) {
                continue;
            }
            $rolesHierarchy[$role->getCode()] = array_keys($role->getChildren());
        }

        $builder->prependExtensionConfig('security', ['role_hierarchy' => $rolesHierarchy]);
    }

    private function loadRolesHierarchy(RolesHierarchyBundleInterface $subject): void
    {
        $rolesHierarchy = $subject->getRolesHierarchy();
        if ($rolesHierarchy) {
            $rolesHierarchy->buildDefinition();
        }
    }

    public function getRolesHierarchy(): RoleDefinitionInterface
    {
        return new RoleDefinition();
    }
}
