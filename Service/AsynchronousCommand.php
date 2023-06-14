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

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class AsynchronousCommand
{
    /**
     * @var ProcessFactory
     */
    private $processFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var string
     */
    private $logsDir;

    /**
     * @var string
     */
    private $phpBin;

    /**
     * @var string
     */
    private $logFilename;

    /**
     * @var bool
     */
    private $disableLog = false;

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

    /**
     * @param bool $disableLog
     * @return self
     */
    public function setDisableLog(bool $disableLog): self
    {
        $this->disableLog = $disableLog;

        return $this;
    }
    /**
     * @param string $command
     * @param array $parameters
     * @return Process
     */
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

    /**
     * @param string $command
     * @param array $parameters
     * @return bool
     */
    public function execute(string $command, array $parameters): bool
    {
        $process = $this->create($command, $parameters);
        $process->setOptions(['create_new_console' => true]);
        $process->start();

        return true;
    }
}
