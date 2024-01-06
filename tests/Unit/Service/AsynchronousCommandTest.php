<?php
namespace Spipu\CoreBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Spipu\CoreBundle\Exception\AsynchronousCommandException;
use Spipu\CoreBundle\Service\AsynchronousCommand;
use Spipu\CoreBundle\Service\ProcessFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class AsynchronousCommandTest extends TestCase
{
    public function testCreateDefaultValues()
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->never())->method('start');

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('create')
            ->with("php bin/console 'test:mock' >> /log/dir/asynchronous-command.log 2>&1", "/project/dir/")
            ->willReturn($process);

        $fileSystem = $this->createMock(Filesystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('appendToFile')
            ->with('/log/dir/asynchronous-command.log');

        /** @var ProcessFactory $processFactory */
        /** @var Filesystem $fileSystem */
        $service = new AsynchronousCommand(
            $processFactory,
            $fileSystem,
            '/project/dir',
            '/log/dir'
        );

        $process = $service->create('test:mock', []);
        $this->assertInstanceOf(Process::class, $process);
    }

    public function testCreateSpecificValues()
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->never())->method('start');

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('create')
            ->with("my-php bin/console 'test:mock' '--id' 1 >> /log/dir/my-file.log 2>&1", "/project/dir/")
            ->willReturn($process);

        $fileSystem = $this->createMock(Filesystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('appendToFile')
            ->with('/log/dir/my-file.log');

        /** @var ProcessFactory $processFactory */
        /** @var Filesystem $fileSystem */
        $service = new AsynchronousCommand(
            $processFactory,
            $fileSystem,
            '/project/dir',
            '/log/dir',
            'my-php',
            'my-file.log'
        );

        $process = $service->create('test:mock', ['--id', 1]);
        $this->assertInstanceOf(Process::class, $process);
    }

    public function testCreateNoLogs()
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->never())->method('start');

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('create')
            ->with("php bin/console 'test:mock' >> /dev/null 2>&1", "/project/dir/")
            ->willReturn($process);

        $fileSystem = $this->createMock(Filesystem::class);
        $fileSystem
            ->expects($this->never())
            ->method('appendToFile');

        /** @var ProcessFactory $processFactory */
        /** @var Filesystem $fileSystem */
        $service = new AsynchronousCommand(
            $processFactory,
            $fileSystem,
            '/project/dir',
            '/log/dir'
        );
        $service->setDisableLog(true);

        $process = $service->create('test:mock', []);
        $this->assertInstanceOf(Process::class, $process);
    }

    public function testExecute()
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->once())->method('start');

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('create')
            ->with("my-php bin/console 'test:mock' >> /log/dir/my-file.log 2>&1", "/project/dir/")
            ->willReturn($process);

        $fileSystem = $this->createMock(Filesystem::class);

        /** @var ProcessFactory $processFactory */
        /** @var Filesystem $fileSystem */
        $service = new AsynchronousCommand(
            $processFactory,
            $fileSystem,
            '/project/dir',
            '/log/dir',
            'my-php',
            'my-file.log'
        );

        $this->assertTrue($service->execute('test:mock', []));
    }

    public function testExecuteKoNoProcess()
    {
        $process = $this->createMock(Process::class);
        $process
            ->expects($this->once())
            ->method('start')
            ->willThrowException(new RuntimeException('Unable to launch a new process.'))
        ;


        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('create')
            ->with("my-php bin/console 'test:mock' >> /log/dir/my-file.log 2>&1", "/project/dir/")
            ->willReturn($process);

        $fileSystem = $this->createMock(Filesystem::class);

        /** @var ProcessFactory $processFactory */
        /** @var Filesystem $fileSystem */
        $service = new AsynchronousCommand(
            $processFactory,
            $fileSystem,
            '/project/dir',
            '/log/dir',
            'my-php',
            'my-file.log'
        );

        $this->expectException(AsynchronousCommandException::class);
        $service->execute('test:mock', []);
    }

    public function testExecuteKoOther()
    {
        $process = $this->createMock(Process::class);
        $process
            ->expects($this->once())
            ->method('start')
            ->willThrowException(new RuntimeException('Other pb.'))
        ;


        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('create')
            ->with("my-php bin/console 'test:mock' >> /log/dir/my-file.log 2>&1", "/project/dir/")
            ->willReturn($process);

        $fileSystem = $this->createMock(Filesystem::class);

        /** @var ProcessFactory $processFactory */
        /** @var Filesystem $fileSystem */
        $service = new AsynchronousCommand(
            $processFactory,
            $fileSystem,
            '/project/dir',
            '/log/dir',
            'my-php',
            'my-file.log'
        );

        $this->expectException(RuntimeException::class);
        $service->execute('test:mock', []);
    }
}
