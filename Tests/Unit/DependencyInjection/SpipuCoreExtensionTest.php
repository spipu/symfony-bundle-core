<?php
namespace Spipu\CoreBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\DependencyInjection\RolesHierarchyExtensionExtensionInterface;
use Spipu\CoreBundle\DependencyInjection\SpipuCoreExtension;
use Spipu\CoreBundle\Service\RoleDefinitionInterface;
use Spipu\CoreBundle\Tests\SymfonyMock;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class SpipuCoreExtensionTest extends TestCase
{
    public function testBase()
    {
        $builder = SymfonyMock::getContainerBuilder($this);

        $extension = new SpipuCoreExtension();

        $this->assertInstanceOf(ExtensionInterface::class, $extension);

        $extension->load([], $builder);
        $extension->prepend($builder);

        $this->assertSame(
            [0 => ['role_hierarchy' => []]],
            $builder->getExtensionConfig('security')
        );
    }

    public function testRoleHierarchy()
    {
        $extension = new SpipuCoreExtension();

        $builder = SymfonyMock::getContainerBuilder($this, [$extension, new \stdClass()]);

        $this->assertInstanceOf(RolesHierarchyExtensionExtensionInterface::class, $extension);
        $this->assertInstanceOf(RoleDefinitionInterface::class, $extension->getRolesHierarchy());

        $extension->load([], $builder);
        $extension->prepend($builder);

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