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

namespace Spipu\CoreBundle\DependencyInjection;

use Spipu\CoreBundle\Service\RoleDefinitionInterface;

interface RolesHierarchiExtensionExtensionInterface
{
    /**
     * @return RoleDefinitionInterface
     */
    public function getRolesHierarchy(): RoleDefinitionInterface;
}
