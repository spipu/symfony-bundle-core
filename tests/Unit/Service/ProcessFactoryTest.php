<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Service\ProcessFactory;
use Symfony\Component\Process\Process;

class ProcessFactoryTest extends TestCase
{
    public function testService()
    {
        $processFactory = new ProcessFactory();
        $process = $processFactory->create('bin/console mock:test --id 1', '/project/dir/');

        $this->assertInstanceOf(Process::class, $process);

        $this->assertSame('bin/console mock:test --id 1', $process->getCommandLine());
        $this->assertSame('/project/dir/', $process->getWorkingDirectory());
    }
}
