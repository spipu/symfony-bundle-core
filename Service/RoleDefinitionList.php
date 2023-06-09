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

namespace Spipu\CoreBundle\Service;

use Spipu\CoreBundle\Entity\Role\Item;

class RoleDefinitionList
{
    /**
     * @var RoleDefinitionInterface[]
     */
    private array $roleDefinitions = [];
    private bool $isAlreadyBuild = false;

    public function __construct(iterable $roleDefinitions)
    {
        foreach ($roleDefinitions as $roleDefinition) {
            $this->addRoleDefinition($roleDefinition);
        }
    }

    private function addRoleDefinition(RoleDefinitionInterface $roleDefinition): void
    {
        $this->roleDefinitions[] = $roleDefinition;
    }

    /**
     * @return RoleDefinitionInterface[]
     */
    public function getDefinitions(): array
    {
        return $this->roleDefinitions;
    }

    public function buildDefinitions(): void
    {
        if (!$this->isAlreadyBuild) {
            foreach ($this->roleDefinitions as $roleDefinition) {
                $roleDefinition->buildDefinition();
            }

            $this->isAlreadyBuild = true;
        }
    }

    /**
     * @param string|null $purpose
     * @param string|null $type
     * @return Item[]
     * @SuppressWarnings(PMD.StaticAccess)
     */
    public function getItems(?string $purpose = null, ?string $type = null): array
    {
        $items = [];
        foreach (Item::getAll() as $item) {
            if ($purpose !== null && $item->getPurpose() !== null && $item->getPurpose() !== $purpose) {
                continue;
            }

            if ($type !== null && $item->getType() !== $type) {
                continue;
            }

            $items[$item->getCode()] = $item;
        }

        return $items;
    }
}
