<?php
namespace Spipu\CoreBundle\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Command\FixturesRemoveCommand;
use Spipu\CoreBundle\Fixture\ListFixture;
use Spipu\CoreBundle\Tests\SymfonyMock;

class FixturesRemoveCommandTest extends TestCase
{
    public function testLoad()
    {
        $inputMock = SymfonyMock::getConsoleInput($this);
        $outputMock = SymfonyMock::getConsoleOutput($this);

        $fixturesMock = $this->createMock(ListFixture::class);
        $fixturesMock->expects($this->never())->method('load');
        $fixturesMock->expects($this->once())->method('remove');

        $command = new FixturesRemoveCommand($fixturesMock);
        $this->assertSame('spipu:fixtures:remove', $command->getName());

        $command->run($inputMock, $outputMock);
    }
}
