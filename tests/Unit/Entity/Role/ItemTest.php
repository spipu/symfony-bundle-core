<?php
namespace Spipu\CoreBundle\Tests\Unit\Entity\Role;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Entity\Role\Item;

class ItemTest extends TestCase
{
    public function testEntity()
    {
        Item::resetAll();
        $this->assertSame([], Item::getAll());

        $main = Item::load('main_code');
        $main
            ->setLabel('main_label')
            ->setType('main_type')
            ->setWeight(10)
            ->addChild('child_code')
        ;

        $child = Item::load('child_code');
        $child
            ->setLabel('child_label')
            ->setType('child_type')
            ->setWeight(20)
            ->addChild('child_code')
        ;

        $this->assertSame('main_code', $main->getCode());
        $this->assertSame('main_label', $main->getLabel());
        $this->assertSame('main_type', $main->getType());
        $this->assertSame(10, $main->getWeight());

        $this->assertSame('child_code', $child->getCode());
        $this->assertSame('child_label', $child->getLabel());
        $this->assertSame('child_type', $child->getType());
        $this->assertSame(20, $child->getWeight());

        $this->assertSame([$child->getCode() => $child], $main->getChildren());
        $this->assertSame([$main->getCode() => $main, $child->getCode() => $child], Item::getAll());

        Item::resetAll();
        $this->assertSame([], Item::getAll());
    }
}
