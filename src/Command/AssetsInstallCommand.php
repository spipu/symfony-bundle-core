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

use Spipu\CoreBundle\Service\Assets;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AssetsInstallCommand extends Command
{
    private Assets $assets;

    public function __construct(
        Assets $assets,
        string $name = null
    ) {
        parent::__construct($name);

        $this->assets = $assets;
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('spipu:assets:install')
            ->setDefinition(array(
                new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', ''),
            ))
            ->setDescription('Installs spipu web assets under a public directory')
            ->setHelp(
                "
The <info>%command.name%</info> command installs spipu web assets into a given
directory (e.g. the <comment>public</comment> directory).

  <info>php %command.full_name% public</info>
"
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sfIo = new SymfonyStyle($input, $output);
        $sfIo->newLine();
        $sfIo->text('Adding Spipu Core assets as <info>hard copies</info>.');

        $this->assets->setTargetDir($input->getArgument('target'));
        $this->assets->installAssets($sfIo);

        $sfIo->newLine();

        return self::SUCCESS;
    }
}
