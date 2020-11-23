<?php
namespace Spipu\CoreBundle\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Command\EncryptorKeyPairCommand;
use Spipu\CoreBundle\Tests\SymfonyMock;

class EncryptorKeyPairCommandTest extends TestCase
{
    public function testLoad()
    {
        $inputMock = SymfonyMock::getConsoleInput($this);
        $outputMock = SymfonyMock::getConsoleOutput($this);

        $command = new EncryptorKeyPairCommand();
        $this->assertSame('spipu:encryptor:generate-key-pair', $command->getName());

        $command->run($inputMock, $outputMock);

        $result = SymfonyMock::getConsoleOutputResult();
        $this->assertSame(7, count($result));
        $this->assertSame('Generate new Encryptor Key Pair.', $result[1]);
    }
}
