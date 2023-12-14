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

interface RolesHierarchyBundleInterface
{
    public function getRolesHierarchy(): ?RoleDefinitionInterface;
}
