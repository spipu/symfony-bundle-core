<?php
namespace Spipu\CoreBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\RolesHierarchyBundleInterface;
use Spipu\CoreBundle\Service\RoleDefinitionInterface;
use Spipu\CoreBundle\SpipuCoreBundle;
use Spipu\CoreBundle\Tests\SymfonyMock;
use Symfony\Component\DependencyInjection\Extension\ConfigurableExtensionInterface;

class SpipuCoreBundleTest extends TestCase
{
    public function testBase()
    {
        $builder = SymfonyMock::getContainerBuilder($this);
        $configurator = SymfonyMock::getContainerConfigurator($this);

        $bundle = new SpipuCoreBundle();

        $this->assertInstanceOf(ConfigurableExtensionInterface::class, $bundle);

        $bundle->loadExtension([], $configurator, $builder);
        $bundle->prependExtension($configurator, $builder);

        $this->assertSame(
            [0 => ['role_hierarchy' => []]],
            $builder->getExtensionConfig('security')
        );
    }

    public function testRoleHierarchy()
    {
        $bundle = new SpipuCoreBundle();
        $configurator = SymfonyMock::getContainerConfigurator($this);

        $builder = SymfonyMock::getContainerBuilder($this, [$bundle, new \stdClass()]);

        $this->assertInstanceOf(RolesHierarchyBundleInterface::class, $bundle);
        $this->assertInstanceOf(RoleDefinitionInterface::class, $bundle->getRolesHierarchy());

        $bundle->loadExtension([], $configurator, $builder);
        $bundle->prependExtension($configurator, $builder);

        $this->assertSame(
            [
                0 => [
                    'role_hierarchy' => [
                        'ROLE_ADMIN'       => ['ROLE_USER'],
                        'ROLE_SUPER_ADMIN' => ['ROLE_ADMIN']
                    ]
                ]
            ],
            $builder->getExtensionConfig('security')
        );
    }
}