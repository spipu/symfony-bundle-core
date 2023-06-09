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

use Exception;
use Spipu\CoreBundle\Service\Encryptor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EncryptorKeyPairCommand extends Command
{
    protected static $defaultName = 'spipu:encryptor:generate-key-pair';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Generate a Encryptor Key Pair')
            ->setHelp("The <info>%command.name%</info> command generates a new encryptor key pair.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $keyPair = (new Encryptor(''))->generateKeyPair();

        $sfIo = new SymfonyStyle($input, $output);
        $sfIo->newLine();
        $sfIo->writeln('Generate new Encryptor <info>Key Pair</info>.');
        $sfIo->newLine();
        $sfIo->success($keyPair);
        $sfIo->newLine();

        return self::SUCCESS;
    }
}
