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

@trigger_error(sprintf('The "%s" class is deprecated, use "%s" instead.', RolesHierarchiExtensionExtensionInterface::class, RolesHierarchyExtensionExtensionInterface::class), E_USER_DEPRECATED);

/**
 * @SuppressWarnings(PMD.LongClassName)
 * @deprecated use RolesHierarchyExtensionExtensionInterface instead.
 */
interface RolesHierarchiExtensionExtensionInterface extends RolesHierarchyExtensionExtensionInterface
{
}
