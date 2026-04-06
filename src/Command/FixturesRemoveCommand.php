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

namespace Spipu\CoreBundle\Command;

use Spipu\CoreBundle\Fixture\ListFixture;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'spipu:fixtures:remove', description: 'Remove the Spipu Fixtures.')]
class FixturesRemoveCommand extends Command
{
    private ListFixture $listFixture;

    public function __construct(
        ListFixture $listFixture,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->listFixture = $listFixture;
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Fixture - Remove - Begin");

        $this->listFixture->remove($output);

        $output->writeln("Fixture - Remove - Finished");

        return self::SUCCESS;
    }
}
