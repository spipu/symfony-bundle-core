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

namespace Spipu\CoreBundle\Service;

use Spipu\CoreBundle\Exception\AsynchronousCommandException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class AsynchronousCommand
{
    private ProcessFactory $processFactory;
    private Filesystem $filesystem;
    private string $projectDir;
    private string $logsDir;
    private string $phpBin;
    private string $logFilename;
    private bool $disableLog = false;

    /**
     * AsynchronousCommand constructor.
     * @param ProcessFactory $processFactory
     * @param Filesystem $filesystem
     * @param string $projectDir
     * @param string $logsDir
     * @param string $phpBin
     * @param string $logFilename
     */
    public function __construct(
        ProcessFactory $processFactory,
        Filesystem $filesystem,
        string $projectDir,
        string $logsDir,
        string $phpBin = 'php',
        string $logFilename = 'asynchronous-command.log'
    ) {
        $this->processFactory = $processFactory;
        $this->filesystem = $filesystem;
        $this->projectDir = $projectDir;
        $this->logsDir = $logsDir;
        $this->phpBin = $phpBin;
        $this->logFilename = $logFilename;
    }

    public function setDisableLog(bool $disableLog): self
    {
        $this->disableLog = $disableLog;

        return $this;
    }

    public function create(string $command, array $parameters): Process
    {
        // Escape parameters.
        foreach ($parameters as &$parameter) {
            if (is_string($parameter)) {
                $parameter = escapeshellarg($parameter);
            }
        }

        // Build Command Line.
        $cmd = array_merge(
            [$this->phpBin, 'bin/console', escapeshellarg($command)],
            $parameters
        );

        // Define LogFile.
        $logFile = '/dev/null';
        if (!$this->disableLog) {
            $logFile = $this->logsDir . DIRECTORY_SEPARATOR . $this->logFilename;
            $content = '[' . date('Y-m-d H:i:d') . '] ' . implode(' ', $cmd) . "\n";

            $this->filesystem->appendToFile($logFile, $content);
        }

        // Add LogFile to Command Line.
        $cmd[] = '>> ' . $logFile;
        $cmd[] = '2>&1';

        // Launch the process.
        return $this->processFactory->create(
            implode(' ', $cmd),
            $this->projectDir . DIRECTORY_SEPARATOR
        );
    }

    public function execute(string $command, array $parameters): bool
    {
        $process = $this->create($command, $parameters);
        $process->setOptions(['create_new_console' => true]);
        try {
            $process->start();
        } catch (RuntimeException $e) {
            if ($e->getMessage() === 'Unable to launch a new process.') {
                throw new AsynchronousCommandException($e->getMessage());
            }

            throw $e;
        }

        return true;
    }
}
