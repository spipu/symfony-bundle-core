<?php
namespace Spipu\CoreBundle\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Command\AssetsInstallCommand;
use Spipu\CoreBundle\Service\Assets;
use Spipu\CoreBundle\Tests\SymfonyMock;

class AssetsInstallCommandTest extends TestCase
{
    public function testLoad()
    {
        $assets = $this->createMock(Assets::class);
        $assets
            ->expects($this->once())
            ->method('setTargetDir')
            ->with('public-mock');

        $assets
            ->expects($this->once())
            ->method('installAssets');

        $command = new AssetsInstallCommand($assets);
        $this->assertSame('spipu:assets:install', $command->getName());

        $input = SymfonyMock::getConsoleInput($this, ['target' => 'public-mock']);
        $output = SymfonyMock::getConsoleOutput($this);

        $command->run($input, $output);
    }
}
