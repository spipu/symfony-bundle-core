<?php

/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spipu\CoreBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class WebTestCase extends BaseWebTestCase
{
    use WebTestCaseTrait;

    /**
     * @var KernelBrowser
     */
    protected static $clientCache;

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::$clientCache = parent::createClient();

        $container = self::getContainer();                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
        $this->prepareDataPrimer(self::$kernel, $container);
    }

    /**
     * @return string The Kernel class name
     */
    protected static function getKernelClass()
    {
        return Kernel::class;
    }

    /**
     * @param array $options
     * @param array $server
     * @return KernelBrowser
     */
    protected static function createClient(array $options = [], array $server = [])
    {
        self::$clientCache->restart();

        return self::$clientCache;
    }

    protected static function loadCommand(string $class, string $name): CommandTester
    {
        $kernel = static::bootKernel();
        $application = new Application($kernel);

        /** @var Command $command */
        $command = static::getContainer()->get($class);
        $application->add($command);

        return new CommandTester($application->find($name));
    }
}
