<?php
/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Spipu\CoreBundle\Service;

use Spipu\CoreBundle\Entity\Role\Item;

class RoleDefinition implements RoleDefinitionInterface
{
    /**
     * @return void
     */
    public function buildDefinition(): void
    {
        Item::load('ROLE_USER')
            ->setLabel('spipu.core.role.user')
            ->setType(Item::TYPE_PROFILE)
            ->setWeight(10);

        Item::load('ROLE_ADMIN')
            ->setLabel('spipu.core.role.admin')
            ->setType(Item::TYPE_PROFILE)
            ->setWeight(50)
            ->addChild('ROLE_USER');

        Item::load('ROLE_SUPER_ADMIN')
            ->setLabel('spipu.core.role.super_admin')
            ->setType(item::TYPE_PROFILE)
            ->setWeight(90)
            ->addChild('ROLE_ADMIN');
    }
}
