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

namespace Spipu\CoreBundle\Fixture;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Fixture Interface
 */
interface FixtureInterface
{
    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @param OutputInterface $output
     * @return void
     */
    public function load(OutputInterface $output) : void;

    /**
     * @param OutputInterface $output
     * @return void
     */
    public function remove(OutputInterface $output) : void;

    /**
     * @return int
     */
    public function getOrder(): int;
}
