<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Entity\Role\Item;
use Spipu\CoreBundle\Service\RoleDefinition;
use Spipu\CoreBundle\Service\RoleDefinitionInterface;
use Spipu\CoreBundle\Service\RoleDefinitionList;

class RoleDefinitionListTest extends TestCase
{
    public function testServiceEmpty()
    {
        Item::resetAll();
        $list = [];
        $service = new RoleDefinitionList($list);
        $result = $service->getDefinitions();
        $this->assertIsArrayWithCount($result, 0);

        $service->buildDefinitions();
        $this->assertIsArrayWithCount(Item::getAll(), 0);

        $result = $service->getItems();
        $this->assertIsArrayWithCount($result, 0);
    }

    public function testServiceNotEmpty()
    {
        $list = [
            new RoleDefinition(),
            new RoleDefinitionFake(),
        ];

        Item::resetAll();
        $service = new RoleDefinitionList($list);
        $result = $service->getDefinitions();
        $this->assertIsArrayWithCount($result, 2);
        $this->assertSame($list[0], $result[0]);
        $this->assertSame($list[1], $result[1]);

        $service->buildDefinitions();
        $this->assertIsArrayWithCount(Item::getAll(), 5);

        $this->assertIsArrayWithCount($service->getItems(null, null), 5);
        $this->assertIsArrayWithCount($service->getItems('admin', null), 4);
        $this->assertIsArrayWithCount($service->getItems('other', null), 2);

        $this->assertIsArrayWithCount($service->getItems(null, Item::TYPE_PROFILE), 4);
        $this->assertIsArrayWithCount($service->getItems('admin', Item::TYPE_PROFILE), 3);
        $this->assertIsArrayWithCount($service->getItems('other', Item::TYPE_PROFILE), 2);

        $this->assertIsArrayWithCount($service->getItems(null, Item::TYPE_ROLE), 1);
        $this->assertIsArrayWithCount($service->getItems('admin', Item::TYPE_ROLE), 1);
        $this->assertIsArrayWithCount($service->getItems('other', Item::TYPE_ROLE), 0);
    }

    private function assertIsArrayWithCount($result, int $count): void
    {
        $this->assertIsArray($result);
        $this->assertSame($count, count($result));
    }
}

class RoleDefinitionFake implements RoleDefinitionInterface
{
    public function buildDefinition(): void
    {
        Item::load('ROLE_TEST')
            ->setLabel('role test')
            ->setType(Item::TYPE_ROLE)
            ->setWeight(1)
            ->addChild('ROLE_ADMIN');

        Item::load('ROLE_OTHER')
            ->setLabel('role other')
            ->setType(Item::TYPE_PROFILE)
            ->setPurpose('other')
            ->setWeight(1)
            ->addChild('ROLE_USER');
    }
}