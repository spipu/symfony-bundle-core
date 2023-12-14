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

namespace Spipu\CoreBundle\Fixture;

use Symfony\Component\Console\Output\OutputInterface;

interface FixtureInterface
{
    public function getCode(): string;

    public function load(OutputInterface $output): void;

    public function remove(OutputInterface $output): void;

    public function getOrder(): int;
}
