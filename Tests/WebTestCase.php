<?php
namespace Spipu\CoreBundle\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

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
}
