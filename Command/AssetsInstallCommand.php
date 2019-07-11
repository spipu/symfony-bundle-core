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

namespace Spipu\CoreBundle\Command;

use Spipu\CoreBundle\Service\Assets;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AssetsInstallCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'spipu:assets:install';

    /**
     * @var Assets
     */
    private $assets;

    /**
     * AssetsInstallCommand constructor.
     * @param Assets $assets
     * @param string|null $name
     */
    public function __construct(
        Assets $assets,
        string $name = null
    ) {
        parent::__construct($name);

        $this->assets = $assets;
    }

    /**
     * Configure the command
     *
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this
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


    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $sfIo = new SymfonyStyle($input, $output);
        $sfIo->newLine();
        $sfIo->text('Adding Spipu Core assets as <info>hard copies</info>.');

        $this->assets->setTargetDir($input->getArgument('target'));
        $this->assets->installAssets($sfIo);

        $sfIo->newLine();
    }
}
