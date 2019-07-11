<?php
namespace Spipu\CoreBundle\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Command\FixturesLoadCommand;
use Spipu\CoreBundle\Fixture\ListFixture;
use Spipu\CoreBundle\Tests\SymfonyMock;

class FixturesLoadCommandTest extends TestCase
{
    public function testLoad()
    {
        $inputMock = SymfonyMock::getConsoleInput($this);
        $outputMock = SymfonyMock::getConsoleOutput($this);

        $fixturesMock = $this->createMock(ListFixture::class);
        $fixturesMock->expects($this->once())->method('load');
        $fixturesMock->expects($this->never())->method('remove');

        $command = new FixturesLoadCommand($fixturesMock);
        $this->assertSame('spipu:fixtures:load', $command->getName());

        $command->run($inputMock, $outputMock);
    }
}
