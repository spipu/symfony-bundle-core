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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixturesLoadCommand extends Command
{
    private ListFixture $listFixture;

    public function __construct(
        ListFixture $listFixture,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->listFixture = $listFixture;
    }

    protected function configure(): void
    {
        $this
            ->setName('spipu:fixtures:load')
            ->setDescription('Load the Spipu Fixtures.')
            ->setHelp('This command will load all the Spipu Fixtures')
        ;
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
        $output->writeln("Fixture - Load - Begin");

        $this->listFixture->load($output);

        $output->writeln("Fixture - Load - Finished");

        return self::SUCCESS;
    }
}
