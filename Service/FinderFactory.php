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

use Symfony\Component\Finder\Finder;

class FinderFactory
{
    /**
     * @return Finder
     * @SuppressWarnings(PMD.StaticAccess)
     */
    public function create(): Finder
    {
        return Finder::create();
    }
}
