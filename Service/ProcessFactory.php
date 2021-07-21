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

use Symfony\Component\Process\Process;

class ProcessFactory
{
    /**
     * @param string $cmd
     * @param string $mainFolder
     * @return Process
     * @SuppressWarnings(PMD.StaticAccess)
     */
    public function create(string $cmd, string $mainFolder): Process
    {
        return Process::fromShellCommandline($cmd, $mainFolder);
    }
}
