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

        $result = array_values(array_filter(SymfonyMock::getConsoleOutputResult()));

        $this->assertGreaterThanOrEqual(2, count($result));
        $this->assertSame('Generate new Encryptor Key Pair.', $result[0]);
        $this->assertStringStartsWith('[OK] ', $result[1]);
    }
}
