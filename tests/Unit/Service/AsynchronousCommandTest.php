<?php

declare(strict_types=1);

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
    public function testExecuteDefaultValues(): void
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->never())->method('start');
        $process->expects($this->once())->method('run')->willReturn(0);

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('create')
            ->with("php bin/console 'test:mock' >> /log/dir/asynchronous-command.log 2>&1 < /dev/null &", "/project/dir/")
            ->willReturn($process);

        $fileSystem = $this->createMock(Filesystem::class);
        $fileSystem
            ->expects($this->once())
            ->method('appendToFile')
            ->with(
                '/log/dir/asynchronous-command.log',
                $this->matchesRegularExpression(
                    "/^\\[\\d{4}-\\d{2}-\\d{2} \\d{2}:\\d{2}:\\d{2}\\] php bin\\/console 'test:mock'\\n$/"
                )
            );

        /** @var ProcessFactory $processFactory */
        /** @var Filesystem $fileSystem */
        $service = new AsynchronousCommand(
            $processFactory,
            $fileSystem,
            '/project/dir',
            '/log/dir'
        );

        $this->assertTrue($service->execute('test:mock', []));
    }

    public function testExecuteSpecificValues(): void
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->never())->method('start');
        $process->expects($this->once())->method('run')->willReturn(0);

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('create')
            ->with("my-php bin/console 'test:mock' '--id' 1 >> /log/dir/my-file.log 2>&1 < /dev/null &", "/project/dir/")
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

        $this->assertTrue($service->execute('test:mock', ['--id', 1]));
    }

    public function testExecuteNoLogs(): void
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->never())->method('start');
        $process->expects($this->once())->method('run')->willReturn(0);

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('create')
            ->with("php bin/console 'test:mock' >> /dev/null 2>&1 < /dev/null &", "/project/dir/")
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

        $this->assertTrue($service->execute('test:mock', []));
    }

    public function testExecute(): void
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->never())->method('start');
        $process->expects($this->once())->method('run')->willReturn(0);

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('create')
            ->with("my-php bin/console 'test:mock' >> /log/dir/my-file.log 2>&1 < /dev/null &", "/project/dir/")
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

    public function testExecuteKoNoProcess(): void
    {
        $process = $this->createMock(Process::class);
        $process
            ->expects($this->once())
            ->method('run')
            ->willThrowException(new RuntimeException('Unable to launch a new process.'))
        ;

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('create')
            ->with("my-php bin/console 'test:mock' >> /log/dir/my-file.log 2>&1 < /dev/null &", "/project/dir/")
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

    public function testExecuteKoOther(): void
    {
        $process = $this->createMock(Process::class);
        $process
            ->expects($this->once())
            ->method('run')
            ->willThrowException(new RuntimeException('Other pb.'))
        ;

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('create')
            ->with("my-php bin/console 'test:mock' >> /log/dir/my-file.log 2>&1 < /dev/null &", "/project/dir/")
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

    public function testExecuteKoExitCode(): void
    {
        $process = $this->createMock(Process::class);
        $process->expects($this->once())->method('run')->willReturn(2);

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('create')
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
        $this->expectExceptionMessage('Unable to launch the asynchronous command - Exit code 2');
        $service->execute('test:mock', []);
    }

    /**
     * Real execution: the call must not wait for the command, and no zombie process must remain at its end
     */
    public function testExecuteRealNoZombie(): void
    {
        $folder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'spipu-async-' . uniqid();
        $filesystem = new Filesystem();
        $filesystem->dumpFile(
            $folder . '/bin/console',
            '<?php file_put_contents(__DIR__ . "/../started.txt", $argv[1]); sleep(2); '
            . 'file_put_contents(__DIR__ . "/../finished.txt", "1");'
        );

        try {
            $service = new AsynchronousCommand(new ProcessFactory(), $filesystem, $folder, $folder, PHP_BINARY);

            $startTime = microtime(true);
            $this->assertTrue($service->execute('test:mock', []));
            $this->assertLessThan(1.5, microtime(true) - $startTime, 'The call must not wait for the command');

            $this->assertTrue($this->waitForFile($folder . '/finished.txt', 10));
            $this->assertSame('test:mock', file_get_contents($folder . '/started.txt'));
            $this->assertStringContainsString("bin/console 'test:mock'", file_get_contents($folder . '/asynchronous-command.log'));

            // Let the command process end, after writing its last file.
            usleep(500000);
            $this->assertSame([], $this->getZombieChildren());
        } finally {
            $filesystem->remove($folder);
        }
    }

    private function waitForFile(string $filename, int $timeout): bool
    {
        $limit = microtime(true) + $timeout;
        while (!is_file($filename)) {
            if (microtime(true) > $limit) {
                return false;
            }
            usleep(100000);
        }

        return true;
    }

    /**
     * @return string[]
     */
    private function getZombieChildren(): array
    {
        exec('ps --ppid ' . getmypid() . ' -o pid=,stat=,comm=', $lines);

        return array_values(array_filter(
            array_map('trim', $lines),
            fn (string $line): bool => (bool) preg_match('/^\d+\s+Z/', $line)
        ));
    }
}
