<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Entity\Role\Item;
use Spipu\CoreBundle\Service\RoleDefinition;
use Spipu\CoreBundle\Service\RoleDefinitionInterface;

class RoleDefinitionUiTest extends TestCase
{
    public static function loadRoles(
        TestCase $testCase,
        RoleDefinitionInterface $roleDefinition,
        bool $cleanNativeRoles = true
    ) {
        Item::resetAll();

        $roleDefinition->buildDefinition();

        $items = Item::getAll();

        Item::resetAll();

        $testCase->assertIsArray($items);

        if ($cleanNativeRoles) {
            unset($items['ROLE_USER']);
            unset($items['ROLE_ADMIN']);
            unset($items['ROLE_SUPER_ADMIN']);
        }

        return $items;
    }

    public function testService()
    {
        $items = self::loadRoles($this, new RoleDefinition(), false);

        $this->assertEquals(3, count($items));
        $this->assertArrayHasKey('ROLE_USER', $items);
        $this->assertArrayHasKey('ROLE_ADMIN', $items);
        $this->assertArrayHasKey('ROLE_SUPER_ADMIN', $items);

        $this->assertEquals(Item::TYPE_PROFILE, $items['ROLE_USER']->getType());
        $this->assertEquals(Item::TYPE_PROFILE, $items['ROLE_ADMIN']->getType());
        $this->assertEquals(Item::TYPE_PROFILE, $items['ROLE_SUPER_ADMIN']->getType());

        Item::resetAll();
    }
}
